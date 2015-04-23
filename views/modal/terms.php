<div class="modal-backdrop fade in" style="height: 100%;"></div>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Quick Add Terms</h4>
        </div>
        <div class="modal-body clearfix tax-modal-body">
            <div class="tax-col2">
                <ul class="tax-tabs">
                    <li class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>

                </ul>
            </div><!--e.tax-col4-->
            <div class="tax-col8">
                <div class="terms-wrap">
                    <p class="tax-description">Press "ENTER" after typing the term...</p>
                    <input type="text" name="term-entry" id="term-entry" placeholder="Type Term to be added..." >
                    <button class="taxonomies-btn taxonomies-btn-info taxonomies-btn-mini" onclick="javascript:add_term(this,event);" >Add</button>

                </div>
            </div><!--e.tax-col6-->
            <div class="clear">&nbsp;</div>
            <div id="taxonomies-bc"></div>

        </div>
        <div class="modal-footer">
            <img id="ajax-loader" src="<?php print $data['loader'];  ?>" alt=""/>&nbsp;
            <button type="button" class="taxonomies-btn taxonomies-btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="taxonomies-btn taxonomies-btn-primary">Save changes</button>
        </div>
    </div>
</div>