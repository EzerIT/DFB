<?php
require_once('head.inc.php');

makeheadstart('Logos');
makeheadend();
makemenus(4);
?>


    <div class="container">
      <div class="row justify-content-center">

        <div class="col-lg-10 col-xl-9">
          <div class="card mt-4">
            <div class="card-body">
              <img class="img-fluid float-right d-none d-lg-block" style="width: 300px; margin-top: 0px; margin-left: 10px" src="img/DeathtoStock_Wired2.jpg" alt="">
              <h1 class="card-title">1871- og 1907-oversættelserne til Logos Bible Software</h1>
              
              <p class="card-text"><em><a href="https://www.logos.com/" target="_blank">Logos Bible
              Software</a></em> er et populært og alsidigt program for teologer og andre
              bibellæsere.</p>

              <p class="card-text">Steen Højgaard har lavet en Logos-version af en ældre dansk
              bibeloversættelse og venligt stillet den til rådighed her. Det drejer sig om
              oversættelsen af det Gamle Testamente fra 1871 og det Nye Testamente fra 1907.</p>


              <p class="card-text">Bogen er lavet som en <em>Personal Book</em>. Det betyder, at den er lavet i Word,
                hvori der er indsat diverse koder for at få den til at opføre sig som var bogen
                købt i Logos Bible Shop. Det betyder at bogen kan linkes til andre bibler, der er
                indeks, bibler kan sammenlignes med <em>Text Comparison</em> og der er forklarende
                noter i nogle af bøgerne. Personal Books kan kun benyttes i pc-versionen. Det
                betyder desværre at man ikke kan bruge dem på sin mobil.</p>

              <p class="card-text">Teksterne er venligst stillet til rådighed af Ulrik Sandborg-Petersen. De ligger
              tilgængeligt på GitHub.</p>

              <h2 class="card-title">Installation</h2>

              <p class="card-text">I det følgende forudsættes det at Logos Bibel Software (version 7, 8 eller 9)
              allerede er installeret på computeren. Hvis det ikke er tilfældet, kan du
              nederst på siden læse mere om hvordan du får programmet.</p>
              
              <p class="card-text">Download
              filen <a href="logos/DA1907-Logos-20201019.zip">DA1907-Logos-20201019.zip</a> og udpak
              den i en mappe. Den indeholder filerne <em>DA1907.docx, DA1907.jpg</em> og <em>Bibel1912.jpg</em>.

              <p class="card-text">Åbn Logos Bible Software.</p>

              <p class="card-text">I menuen <em>Tools</em> vælges <em>Personal Books</em>. Herved
              åbnes en fane med Personal Books.</p>

              <p class="card-text">Klik på <em>Add Book</em> i fanen Personal Books. Du vil nu se dette billede:</p>

              <img class="img-fluid" style="width: 100%; margin-top: 0px; margin-bottom: 10px" src="logos/logos1.png" alt="">

              <p class="card-text">Udfyld felterne således:</p>
              <ul>
                <li><em>Title</em> sættes til »DA1907«.</li>
                <li><em>Type</em> sættes til <em>Bible</em>.</li>
                <li><em>Language</em> sættes til <em>Danish</em>.</li>
                <li><em>Description</em> sættes til »GT 1871 / NT 1907«.</li>
                <li>Klik på <em>Change</em> under bog-ikonen og vælg filen <em>DA1907.jpg</em> fra
                    den ZIP-fil du downloadede og udpakkede ovenfor. (Alternativt kan du vælge filen <em>Bibel1912.jpg</em>
                    hvis du ønsker et andet billede.)</li>
                <li>Klik på <em>Add file...</em> og vælg filen <em>DA1907.docx</em> fra den ZIP-fil
                  du downloadede udpakkede ovenfor.</li>
              </ul>

              <p class="card-text">Det udfyldte skærmbillede vil nu se således ud:</p>
              
              <img class="img-fluid" style="width: 100%; margin-top: 0px; margin-bottom: 10px" src="logos/logos2.png" alt="">

              <p class="card-text">Klik på <em>Build book.</em> Bogen bliver nu lagt ind i Logos
              Bible Software. Processen tager et par minutter. (Under processen oprettes en log-fil, som vil indeholde nogle advarsler om »duplicate Milestone«. Disse advarsler kan du ignorere.)</p>

              <p class="card-text">Du vil fremover kunne finde bogen i dit Logos-bibliotek under navnet »DA1907«.</p>
              
              
              <h2 class="card-title">Jamen, jeg har ikke Logos Bible</h2>

              <p class="card-text">Det er ikke noget problem. Du kan nøjes med den gratis version
            til Windows eller Mac. Gå ind på <a href="https://www.logos.com/"
            target="_blank">www.logos.com</a>. I feltet <em>Search</em> skriver du: »logos basic«.
            Tryk på retur-tasten. Nu kan du se programmet:</p>

              <img class="img-fluid" style="width: 100%; margin-top: 0px; margin-bottom: 10px" src="logos/logos9basic.png" alt="">

              <p class="card-text">Klik på <em>Add to cart</em>. Køb produktet og installér det.
                Herefter kan du installere den gamle danske bibeloversættelse. Men hvorfor ikke lige
                bruge $27 på den autoriserede danske bibel?
                Den <a href="https://www.logos.com/product/25687/bibelen-den-hellige-skrifts-kanoniske-boger"
                target="_blank">findes i Logos Bible Shop</a>, og derfor kan den også bruges på din
                mobil, iPad osv. Du må benytte den på alle dine enheder. Der findes mange videoer om
                hvordan man benytter Logos Bible. Dan Hessellund har lavet nogle. Besøg hans side
                på <a href="http://danhessellund.dk/" target="_blank">danhessellund.dk</a>.</p>
              
            </div>
          </div>
        </div>
 
      </div><!--End of row-->
    </div><!--End of container-->

<?php
endbody();
?>
