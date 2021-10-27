<ul class="list-group">
    <{foreach from=$block item=action}>
        <li class="list-group-item">
            <span class="badge badge-info">
                <{$action.action_date|substr:0:-3}>
                <small>
                    名額<{$action.number}>人，已報名<{$action.signup|@count}>人
                    <{if $action.candidate}> <span data-toggle="tooltip" title="可侯補人數">(<{$action.candidate}>) </span><{/if}>
                </small>
            </span>
            <div>
                <{if $action.enable &&($action.number + $action.candidate) > $action.signup|@count  && $action.end_date|strtotime >= $smarty.now}>
                    <i class="fa fa-check text-success" data-toggle="tooltip" title="報名中" aria-hidden="true"></i>
                <{else}>
                    <i class="fa fa-times text-danger" data-toggle="tooltip" title="無法報名"  aria-hidden="true"></i>
                <{ /if}>
                <a href="<{$xoops_url}>/modules/eric_signup/index.php?id=<{$action.id}>"><{$action.title}></a>
        </div>
        </li>
    <{ /foreach}>

</ul>

