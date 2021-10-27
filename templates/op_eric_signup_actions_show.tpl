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
                            <{if ($smarty.session.can_add && $uid==$now_uid) || $signup_data.uid==$now_uid}>
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
                <{if $smarty.session.can_add && $uid==$now_uid}>
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

                <td><{$signup_data.signup_date}></td>
            </tr>
        <{/foreach}>
    </tbody>
</table>


<{if $smarty.session.can_add  && $uid==$now_uid}>
    <div class="bar">
        <a href="javascript:del_action('<{$id}>')" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> 刪除活動</a>
        <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_actions_edit&id=<{$id}>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> 編輯活動</a>
    </div>
<{/if}>
