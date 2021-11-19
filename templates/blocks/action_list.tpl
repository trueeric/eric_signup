<ul class="list-group">
    <{foreach from=$block item=action}>
        <li class="list-group-item">
            <span class="badge badge-info">
                <{$action.action_date|substr:0:-3}>
                <small>
                    <{$smarty.const._MB_ERIC_SIGNUP_ACTION_QUOTA}><{$action.number}> <{$smarty.const._MB_ERIC_SIGNUP_ACTION_PEOPLE}> <{$smarty.const._MB_ERIC_SIGNUP_ACTION_ENROLLED}><{$action.signup_count}> <{$smarty.const._MB_ERIC_SIGNUP_ACTION_PEOPLE}>
                    <{if $action.candidate}> <span data-toggle="tooltip" title= "<{$smarty.const._MB_ERIC_SIGNUP_ACTION_NUMBER_OF_AVAILABLE CANDIDATES}>" >(<{$action.candidate}>) </span><{/if}>
                </small>
            </span>
            <div>
                <{if $action.enable &&($action.number + $action.candidate) > $action.signup_count  && $action.end_date|strtotime >= $smarty.now}>
                    <i class="fa fa-check text-success" data-toggle="tooltip" title="<{$smarty.const._MB_ERIC_SIGNUP_ACTION_ENROLLED}>" aria-hidden="true"></i>
                <{else}>
                    <i class="fa fa-times text-danger" data-toggle="tooltip" title="<{$smarty.const._MB_ERIC_SIGNUP_ACTION_UNABLE_TO_ENROLL}>" aria-hidden="true"></i>
                <{ /if}>
                <a href="<{$xoops_url}>/modules/eric_signup/index.php?id=<{$action.id}>"><{$action.title}></a>
        </div>
        </li>
    <{ /foreach}>

</ul>

