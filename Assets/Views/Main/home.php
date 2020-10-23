
<?php WEB::load_view("Part","head"); ?>
<?php WEB::load_view("Part","nav"); ?>

<div class="container">
    <div class="row">
    <div class="col-sm-12">
      <div class="card mt-3 tab-card">
        <div class="card-header tab-card-header">
          <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
            <li class="nav-item" onclick="SYS.XHRFct('colors/instructions','TabContainer');">
                <a class="nav-link" data-toggle="tab" role="tab"  aria-selected="true">Instructions</a>
            </li>
            <li class="nav-item" onclick="SYS.XHRFct('colors/new_image','TabContainer');">
                <a class="nav-link" data-toggle="tab" role="tab"  aria-selected="false">Upload Image</a>
            </li>
            <li class="nav-item" onclick="SYS.XHRFct('colors/load_images','TabContainer');">
                <a class="nav-link" data-toggle="tab" role="tab"  aria-selected="false">Read Pixels</a>
            </li>
          </ul>
        </div>
        <div class="tab-content" id="myTabContent">
          <div id="TabContainer" class="tab-pane fade show active p-3"  role="tabpanel">
                <script>SYS.XHRFct('colors/instructions','TabContainer');</script>
          </div>
        </div>
      </div>
    </div>
    </div>
</div>
<?php WEB::load_view("Part","foot"); ?>