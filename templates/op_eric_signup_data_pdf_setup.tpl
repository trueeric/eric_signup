<h2 class="my"><{$action_title}>簽到表欄位定</h2>
<form action="index.php" method="post" id="myForm" enctype="multipart/form-data" class="form-horizontal">

    <{$tmt_box}>
    <input type="hidden" name="op" value="eric_signup_pdf_setup_save">
    <input type="hidden" name="action_id" value="<{$action.id}>">
    <div class="bar">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save" aria-hidden="true">儲存並下載「<{$action_title}>」簽到表</i> >
        </button>
    </div>
</form>