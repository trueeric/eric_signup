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
12345：<{$detail}>
</div>


<h3 class="my">
    已報名資料表
    <small>
        <i class="fa fa-calendar" aria-hidden="true"></i>報名截止日期：<{$end_date}>
        <i class="fa fa-users" aria-hidden="true"></i>報名人數上限：<{$number}>
    </small>
</h3>


<{if $smarty.session.eric_signup_adm}>
    <div class="bar">
        <a href="index.php?op=eric_signup_actions_edit&id=<{$id}>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> 編輯活動</a>
    </div>
<{/if}>
