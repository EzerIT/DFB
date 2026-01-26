<?php
require_once('head.inc.php');

makeheadstart('Redaktionsgruppe');
makeheadend();
makemenus(3);
?>


<script>
function cloakEmail(elementId, domain, user, tld) {
    const email = user + "@" + domain + "." + tld;
    const link = document.createElement("a");
    link.href = "mailto:" + email;
    link.textContent = email;
    document.getElementById(elementId).appendChild(link);
 }

 $(function() {
     cloakEmail("jbk","dbi","jbk","edu")
     cloakEmail("tt","tondering","trine","dk")
     cloakEmail("nwn","dbi","nwn","edu")
     cloakEmail("tk","outlook","torbenkjaer2021","dk")
     cloakEmail("ct","tondering","claus","dk")
 });
 
</script>

<div class="container">
    <div class="row justify-content-center">

    <div class="col-lg-10 col-xl-9">
      <div class="card mt-4">
        <div class="card-body">
           <img class="img-fluid float-right d-none d-lg-block" style="width: 300px; margin-top: 0px; margin-left: 10px" src="img/pexels-sora-shimazaki-5668490.jpg" alt="">
         <h1>Redaktions&shy;gruppe</h1>

         <p class="pt-lg-3">Den Frie Bibels redaktionsgruppe har fire ansvarsområder, der er besat således:</p>

         <ul class="pt-lg-2">
             <li><strong>Projektansvarlig:</strong> Jens Bruun Kofoed (<span id="jbk"></span>)</li>
             <li><strong>Sprogredaktør:</strong> Trine Tøndering (<span id="tt"></span>)</li>
             <li><strong>Fagredaktører:</strong></li>
             <ul>
                 <li>For det Gamle Testamente: Nicolai Winther-Nielsen (<span id="nwn"></span>)</li>
                 <li>For det Nye Testamente: Torben Kjær (<span id="tk"></span>)</li>
             </ul>
             <li><strong>It-ansvarlig:</strong> Claus Tøndering (<span id="ct"></span>)</li>
         </ul>
        </div>
      </div>
    </div>
    
  </div><!--End of row-->
</div><!--End of container-->

<?php
endbody();
?>
