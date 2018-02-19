backend default {
  .host = "static-nginx";
  .port = "80";
}

sub vcl_fetch {
    # 1 minute
   set beresp.ttl = 60 s;
}

sub vcl_recv {

   if (req.request == "POST")
   {
       return (pass);
   }

   unset req.http.Cookie;
}

