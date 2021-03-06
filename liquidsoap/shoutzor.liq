set("log.file.path", "/dev/null")
set("log.stdout", true)
set("server.telnet", false)
set("server.socket",true)                           #use a socket
set("server.socket.path", '/home/shoutzor/socket')  #the path to a socket
set("server.socket.permissions",511)                #the permission for the file, here is the equivalent to 777 .Ideally you would use a more restrictive permission

set("audio.converter.samplerate.libsamplerate.quality", "best")

# Add a skip function to a source
# when it does not have one
# by default
def add_skip_command(s) =
 # A command to skip
 def skip(_) =
   source.skip(s)
   "Done!"
 end
 # Register the command:
 server.register(namespace="#{source.id(s)}",
                 usage="skip",
                 description="Skip the current song.",
                 "skip",skip)
end

# Mixes two streams, with faded transitions between the state when only the
# normal stream is available and when the special stream gets added on top of
# it.
# @category Source / Track Processing
# @param ~delay   Delay before starting the special source.
# @param ~p       Portion of amplitude of the normal source in the mix.
# @param ~normal  The normal source, which could be called the carrier too.
# @param ~special The special source.
def smooth_add(~delay=0.5,~p=0.2,~normal,~special)
  d = delay
  fade.final = fade.final(duration=d*2.)
  fade.initial = fade.initial(duration=d*2.)
  q = 1. - p
  c = amplify
  fallback(track_sensitive=false,
           [special,normal],
           transitions=[
             fun(normal,special)->
               add(normalize=false,
                   [c(p,normal),
                    c(q,fade.final(type="sin",normal)),
                    sequence([blank(duration=d),c(q,special)])]),
             fun(special,normal)->
               add(normalize=false,
                   [c(p,normal),
                    c(q,fade.initial(type="sin",normal))])
           ])
end

def smart_crossthing (~start_next=5.,~fade_in=3.,~fade_out=3.,
                     ~default=(fun (a,b) -> sequence([a, b])),
                     ~high=-15., ~medium=-32., ~margin=4.,
                     ~width=2.,~conservative=false,s)

  fade.out = fade.out(type="sin",duration=fade_out)
  fade.in  = fade.in(type="sin",duration=fade_in)
  add = fun (a,b) -> add(normalize=false,[b, a])
  log = log(label="smart_crossfade")


  def transition(a,b,ma,mb,sa,sb)
    list.iter(fun(x)-> log(level=4,"Before: #{x}"),ma)
    list.iter(fun(x)-> log(level=4,"After : #{x}"),mb)

    #if ma["type"] == "jingles" or mb["type"] == "jingles" then
    #if mb["type"] == "jingles" then
    #if mb["type"] == "ongeldig" then
    #  log("New file is a jingle: sequenced transition.")
    #  sequence([sa, sb])
    #else
	    if
	      # If A and B are not too loud and close, fully cross-fade them.
	      a <= medium and b <= medium and abs(a - b) <= margin
	    then
	      log("Old <= medium, new <= medium and |old-new| <= margin.")
	      log("Old and new source are not too loud and close.")
	      log("Transition: crossed, fade-in, fade-out.")
	      add(fade.out(sa),fade.in(sb))

	    elsif
	      # If B is significantly louder than A, only fade-out A.
	      # We don't want to fade almost silent things, ask for >medium.
	      b >= a + margin and a >= medium and b <= high
	    then
	      log("new >= old + margin, old >= medium and new <= high.")
	      log("New source is significantly louder than old one.")
	      log("Transition: crossed, fade-out.")
	      add(fade.out(sa),sb)

	    elsif
	      # Opposite as the previous one.
	      a >= b + margin and b >= medium and a <= high
	    then
	      log("old >= new + margin, new >= medium and old <= high")
	      log("Old source is significantly louder than new one.")
	      log("Transition: crossed, fade-in.")
	      add(sa,fade.in(sb))

	    elsif
	      # Do not fade if it's already very low.
	      b >= a + margin and a <= medium and b <= high
	    then
	      log("new >= old + margin, old <= medium and new <= high.")
	      log("Do not fade if it's already very low.")
	      log("Transition: crossed, no fade.")
	      add(sa,sb)

	    # What to do with a loud end and a quiet beginning ?
	    # A good idea is to use a jingle to separate the two tracks,
	    # but that's another story.

	    else
	      # Otherwise, A and B are just too loud to overlap nicely,
	      # or the difference between them is too large and overlapping would
	      # completely mask one of them.
	      log("No transition: using default.")
	      default(sa, sb)
	    end
	#end
  end

  smart_cross(width=width, duration=start_next, conservative=conservative,
              transition,s)
end

def append_type(m) =
	[("type", "jingles")]
end

def blank_handler()
  system("curl http://localhost/liquidsoap/autofix/")
end

def metadatahandler(m) 
	system("curl http://localhost/liquidsoap/getnexttrack/ > /dev/null &")
end

#In case nothing works anymore, say this
unfallibleSource = single("say: Shoutzor is experiencing technical difficulties, please stand by")

#jingles
jingles = playlist(conservative=true, mode="randomize", "/home/shoutzor/jingles.pls")

#tell the script this source contains the jingles
jingles = map_metadata(append_type, jingles)

#Requester input
input = request.queue(id="shoutzorqueue")

# Attach a skip command to the source input:
add_skip_command(input)

input = on_metadata(metadatahandler, input)

#add Jingles to the main source
input = fallback([delay(840., jingles), input])

#apply crossfade
input = smart_crossthing(start_next=5., fade_out=7., fade_in=5., conservative=true, input)

#give control over the channels
input = mix(id="sound", [input, unfallibleSource])

input = on_blank(max_blank=10., blank_handler, input)

#stream output
output.icecast(
  %mp3(bitrate=192), 
  mount="/streaminput",
  id="icecast",
  host="localhost", port=1337, password="G2QRV2qcawEtgy5wbhr5HEG4sF5WFRDcDEZFaWD3w",
  input)
