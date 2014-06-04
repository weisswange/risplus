<h2>{$file->getFilename()}</h2>

{if $file_error_empty_parameter != ''}
    <p class="lead bg-danger">{$file_error_empty_parameter}</p>
{elseif $file_error_wrong_id != ''}
    <p class="lead bg-danger">{$file_error_wrong_id}</p>
{else}
    <div class="col-md-8">

        {literal}
        <script type='text/javascript'>
            $(document).ready(function()
            {
                $('a#modal-body-{/literal}{$file->getId()}{literal}').media({width:800, height:1100});
            });
        </script>
        {/literal}

        <a id="modal-body-{$file->getId()}" class="media" href="/downloads/{$file->getFilename()}"></a>
    </div>

    <div class="col-md-4">
        <p class="lead bg-info">Vorlagen zum Dokument</p>
        {foreach from=$file_vorlagen item=dataset}
        <dl>
            <dt>Name</dt><dd>{$dataset.name} <a class="btn btn-default" href="/vorlagen/{$dataset.id}">Anzeigen</a></dd>
            <dt>Titel</dt><dd>{$dataset.subject}</dd>
            <dt>Datum</dt><dd>{$dataset.date|date_format:"%d.%m.%Y"}</dd>
        </dl>
        {/foreach}
    </div>
{/if}