<h2 class="my">
    <{if $block.enable && ($block.number + $block.candidate)  > $block.signup_count  && $block.end_date|strtotime >= $smarty.now}>
    <i class="fa fa-check text-success" aria-hidden="true"></i>
    <{else}>
        <i class="fa fa-times text-danger" aria-hidden="true"></i>
    <{/if}>
    <{$block.title}>

</h2>

<div class="alert alert-info">
<{$block.detail}>
</div>


<h4 class="my">

    <small>
        <div><i class="fa fa-calendar" aria-hidden="true"></i>活動日期 :<{$block.action_date|substr:5:5}></div>
        <div><i class="fa fa-calendar" aria-hidden="true"></i>報名截止日期:<{$block.end_date|substr:5:-3}></div>
        <div>
            <i class="fa fa-users" aria-hidden="true"></i>報名狀況:  <{$block.signup_count}>/<{$block.number}>
            <{if $block.candidate}> <span data-toggle="tooltip" title="可侯補人數">(<{$block.candidate}>) </span><{/if}>
        </div>
    </small>
</h4>

<div class="text-center my-2">
    <a href="<{$xoops_url}>/modules/eric_signup/index.php?op=eric_signup_data_create&action_id=<{$block.id}>" class="btn btn-lg btn-info <{if !($block.enable && ($block.number + $block.candidate)  > $block.signup_count && $xoops_isuser && $block.end_date|strtotime >= $smarty.now)}>disabled <{/if}>"><i class="fa fa-plus" aria-hidden="true"></i> 立即報名</a>
</div>








