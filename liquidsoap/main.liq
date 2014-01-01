set("log.file.path", "/dev/null")
set("log.stdout", true)
set("server.telnet", false)
set("server.socket",true)                           #use a socket
set("server.socket.path", '/home/shoutzor/socketmain')  #the path to a socket
set("server.socket.permissions",511)                #the permission for the file, here is the equivalent to 777 .Ideally you would use a more restrictive permission

#In case nothing works anymore, say this
unfallibleSource = single("say: Shoutzor is experiencing technical difficulties, please stand by")

#give control over the channels
input = mix(id="nothingness", [unfallibleSource])

live = input.harbor("streaminput",port=1337,password="G2QRV2qcawEtgy5wbhr5HEG4sF5WFRDcDEZFaWD3w")

input = fallback(track_sensitive=false, [live, input])

#stream output
output.icecast(
  %mp3(bitrate=192), 
  mount="/stream",
  id="fallbackz",
  host="localhost", port=8000, password="JH4ii2CTLbzJLBOb099k",
  input)