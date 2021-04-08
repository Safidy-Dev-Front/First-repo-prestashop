<!-- Block module -->
<div id="ps_hello_block_home" class="block">
    <h4>{l s='Welcome!' mod='ps_hello'}</h4>
    <div class="block_content">
        <p>Hello,
            {if isset($module_hello_world) && $module_hello_world}
                {$module_hello_world}
            {else}
                World
            {/if}
            !
        </p>
        <ul>
            <li><a href="{$module_link}" title="Click this link">Click me!</a></li>
        </ul>
    </div>
</div>