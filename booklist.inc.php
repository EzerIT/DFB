<!-- Modal dialog displaying abbreviations of books of the Bible -->
<div class="modal" id="bibleBooksModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Forkortelser</h5>
        <button type="button" class="close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <div class="modal-body">
          <table class="mx-auto">
          <?php
          foreach ($title as $abb => $name) {
              if ($abb!=='GT' && $abb!=='NT')
                  echo "<tr><td>$abbrev[$abb]</td><td class=\"pl-3\">$name</td>\n";
          }
          ?>
          </table>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Luk</button>
      </div>
    </div>
  </div>
</div>
