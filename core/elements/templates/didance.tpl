{extends 'file:templates/base.tpl'}
{block 'content'}
    {include 'file:chunks/main-page/utp.tpl'}
    {include 'file:chunks/main-page/about-didance.tpl'}
    {include 'file:chunks/main-page/video-block.tpl'}
    {include 'file:chunks/main-page/this-Is-Didance.tpl'}
    {$_modx->runSnippet('!InstagramLatestPosts@PropertySet',[
    'accountName' => 'di_dance',
    'limit' => 6,
    'cacheEnabled' => 0
    ])}
    {include 'file:chunks/main-page/main-page-special.tpl'}
{/block}
{block 'modals'}
    <!--Video modal-->
    <div class="video-modal modal fade modal-vcenter" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <iframe id="cartoonVideo" src="//vk.com/video_ext.php?oid=3293161&id=456239059&hash=679073a52865166f&hd=2" width="853" height="480" frameborder="0"  allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
    <!--End of Video modal-->
{/block}