<h2 class="my">
    <{if $enable}>
    <i class="fa fa-check text-success" aria-hidden="true"></i>
    <{else}>
        <i class="fa fa-times text-danger" aria-hidden="true"></i>
    <{/if}>
    <{$title}>
    <small><i class="fa fa-calendar" aria-hidden="true"></i>活動日期 ：<{$action_date}></fa-cale></small>
</h2>

<div class="alert alert-info">
<{$detail}>
</div>
<!-- AddToAny BEGIN -->
<div class="a2a_kit a2a_kit_size_32 a2a_default_style">
    <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
    <a class="a2a_button_printfriendly"></a>
    <a class="a2a_button_facebook"></a>
    </div>
    <script async src="https://static.addtoany.com/menu/page.js"></script>
<!-- AddToAny END -->
<{$files}>


<h3 class="my">
    已報名資料表
    <small>
        <i class="fa fa-calendar" aria-hidden="true"></i>報名截止日期：<{$end_date}>
        <i class="fa fa-users" aria-hidden="true"></i>報名人數上限：<{$number}>
        <{if $candidate}> <span data-toggle="tooltip" title="可侯補人數">(<{$candidate}>) </span><{/if}>
    </small>
</h3>

<table data-toggle="table" data-pagination="true" data-search="true"   data-mobile-responsive="true">
    <thead>
        <tr>
            <{foreach from=$signup.0.tdc key=col_name item=user name=tdc}>
                <th data-sortable="true" ><{$col_name}></th>
            <{/foreach}>
            <th>錄取</th>
            <th>報名日期</th>
        </tr>
    </thead>
    <tbody>
        <{foreach from=$signup  item=signup_data}>
            <tr>
                <{foreach from=$signup_data.tdc key=col_name item=user_data}>

                    <td>
                        <{foreach from=$user_data item=data}>
                            <{if ($smarty.session.can_add && $action.uid==$now_uid )|| $smarty.session.eric_signup_adm || $signup_data.uid==$now_uid}>
                                <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_data_show&id=<{$signup_data.id}>"><div><{$data}></div></a>

                            <{else}>
                                <{if strpos($col_name,'姓名')!==false}>
                                    <div><{$data|substr_replace:'O':3:3}></div>
                                <{else}>
                                    <div>***</div>
                                <{/if}>
                            <{/if}>
                        <{/foreach}>
                    </td>

                <{/foreach}>
                <{if ($smarty.session.can_add && $action.uid==$now_uid )|| $smarty.session.eric_signup_adm}>
                    <td>
                        <{if $signup_data.accept==='1'}>
                            <div class="text-primary">錄取</div>
                            <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_data_accept&id=<{$signup_data.id}>&action_id=<{$id}>&accept=0" class="btn btn-sm btn-warning">改成未錄取</a>
                        <{elseif $signup_data.accept==='0'}>
                            <div class="text-danger">未錄取</div>
                            <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_data_accept&id=<{$signup_data.id}>&action_id=<{$id}>&accept=1" class="btn btn-sm btn-success">改成錄取</a>
                        <{else}>
                            <div class="text-muted">尚未設定</div>
                            <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_data_accept&id=<{$signup_data.id}>&action_id=<{$id}>&accept=0" class="btn btn-sm btn-warning">改成未錄取</a>
                            <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_data_accept&id=<{$signup_data.id}>&action_id=<{$id}>&accept=1" class="btn btn-sm btn-success">改成錄取</a>
                        <{/if}>
                    </td>>
                <{/if}>
                <td>
                    <{$signup_data.signup_date}>
                    <{if $signup_data.tag}>
                        <div><span class="badge bg-primary text-dark"><{$signup_data.tag}></span></div>
                    <{/if}>
                </td>
            </tr>
        <{/foreach}>
    </tbody>
</table>


<{if ($smarty.session.can_add && $action.uid == $now_uid) || $smarty.session.eric_signup_adm}>
    <div class="bar">
        <a href="javascript:del_action('<{$id}>')" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> 刪除活動</a>
        <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_actions_edit&id=<{$id}>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> 編輯活動</a>
        <a href="<{$xoops_url}>/modules/eric_signup/csv.php?id=<{$id}>&type=signup" class="btn btn-primary"><i class="fa fa-file-o" aria-hidden="true"></i> 匯出報名名單 CSV</a>
        <a href="<{$xoops_url}>/modules/eric_signup/excel.php?id=<{$id}>&type=signup" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> 匯出報名名單 EXCEL</a>
        <a href="<{$xoops_url}>/modules/eric_signup/pdf.php?id=<{$id}>" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> 匯出報名名單 PDF</a>
    </div>

    <div class="bar">
        <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_data_pdf_setup&id=<{$id}>" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> 匯出簽到表 PDF</a>
    </div>

    <form action="index.php" method="post" class="my-4" enctype="multipart/form-data" >
        <div class="input-group">
            <div class="input-group-prepend input-group-addon">
                <span class="input-group-text">匯入報名名冊CSV</span>
            </div>
            <input type="file" name="csv" class="form-control" accept="text/csv">
            <div class="input-group-append input-group-btn">
                <input type="hidden" name="op" value="eric_signup_data_preview_csv">
                <input type="hidden" name="id" value="<{$id}>">
                <button type="submit" class="btn btn-primary">匯入CSV</button>
                <a href="<{$xoops_url}>/modules/eric_signup/csv.php?id=<{$id}>" class="btn btn-secondary">下載範例格式</a>
            </div>
        </div>
    </form>

    <form action="index.php" method="post" class="my-4" enctype="multipart/form-data" >
        <div class="input-group">
            <div class="input-group-prepend input-group-addon">
                <span class="input-group-text">匯入報名名冊EXCEL</span>
            </div>
            <input type="file" name="excel" class="form-control" accept=".xlsx">
            <div class="input-group-append input-group-btn">
                <input type="hidden" name="op" value="eric_signup_data_preview_excel">
                <input type="hidden" name="id" value="<{$id}>">
                <button type="submit" class="btn btn-primary">匯入EXCEL</button>
                <a href="<{$xoops_url}>/modules/eric_signup/excel.php?id=<{$id}>" class="btn btn-secondary"><i class="fa fa-file-excel-o" aria-hidden="true"></i> 下載範例格式</a>
            </div>
        </div>
    </form>



<{/if}>
