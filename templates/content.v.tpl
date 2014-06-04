{if $vorlage_error_empty_parameter != ''}
    <p class="lead bg-danger">{$vorlage_error_empty_parameter}</p>
{elseif $vorlage_error_wrong_id != ''}
    <p class="lead bg-danger">{$vorlage_error_wrong_id}</p>
{elseif $vorlage_show_form == false}
    <h2>{$vorlage->getName()} <small>{$vorlage->getDate()|date_format:"%d.%m.%Y"}</small></h2>
    <div class="col-md-8">
        <p class="lead">{$vorlage->getSubject()}</p>

        {literal}
        <script type='text/javascript'>
            $(document).ready(function()
            {
                $("#documentlist").css('word-wrap', 'break-word');
                {/literal}
                {foreach from=$vorlagen_files item=dataset}
                {literal}
                $('button#modal-{/literal}{$dataset.id}{literal}').on('click', function()
                {
                    $('div.media').remove();
                    $('#modalboxes').prepend('<a id="modal-body-{/literal}{$dataset.id}{literal}" class="media" href="/downloads/{/literal}{$dataset.filename}{literal}"></a>');
                    $('a#modal-body-{/literal}{$dataset.id}{literal}').media({width:800, height:1100});
                    $('button.modalbuttons').removeClass('btn-info');
                    $('button.modalbuttons').addClass('btn-default');
                    $('button#modal-{/literal}{$dataset.id}{literal}').removeClass('btn-default');
                    $('button#modal-{/literal}{$dataset.id}{literal}').addClass('btn-info');
                });
                {/literal}
                {/foreach}
                {literal}
            });
        </script>
        {/literal}
        <div id="modalboxes">
        </div>
    </div>

    <div class="col-md-4" id="documentlist">
        <form id="form-sidebar" role="form" action="/vorlagen/" method="post">
       		<div class="form-group {$search_error_input_empty}">
       			<input name="s" type="text" class="form-control" id="s" placeholder="Neue Suche" {if $search_input_s != ""}value="{$search_input_s}"{/if} />
       	  	</div>
            <input type="submit" class="btn btn-block btn-primary btn-sm" value="Start" />
       	</form>
        <p class="lead bg-info">Dokumente zur Vorlage</p>
        {foreach from=$vorlagen_files item=dataset}
        <dl>
            <dt>Name</dt><dd>{$dataset.filename}<br />
                <button class="btn btn-default modalbuttons" id="modal-{$dataset.id}" href="/dokumente/{$dataset.id}">Anzeigen</button>
                <a class="btn btn-default" href="/dokumente/{$dataset.id}">Details</a></dd>
        </dl>

        {/foreach}
    </div>
{elseif $vorlage_show_form == true}
    <div class="col-md-9">
        {if $search_error_input_empty == 'has-error'}
        {literal}
        <script>
            $(document ).ready(function() {
                $( "#errormessage" ).effect('shake', {direction: "ltr", times: 4, distance: 5}, 1000);
            });
        </script>
        {/literal}
        <p class="text-danger" id="errormessage">Bitte eine Vorlagennummer oder ein Wort in das Suchfeld eingeben</p>
       	{/if}
        <form role="form" action="" method="post">
       		<div class="form-group {$search_error_input_empty}">
       			<input name="s" type="text" class="form-control input-lg" id="s" placeholder="Nummer der Vorlage oder Suchwort" {if $search_input_s != ""}value="{$search_input_s}"{/if} />
       	  	</div>
            <input type="submit" class="btn btn-block btn-primary btn-sm" value="Start" />
       	</form>
    </div>
    <div class="col-md-3" id="bigteaser">
    	<p class="lead">Recherche in Vorlagen des Ratsinformationssystems der Stadt Halle</p>
        <a class="btn btn-default btn-block" href="/">In Dokumenten suchen</a>
    </div>
{/if}

{if $search_results_count > 0}
<div class="col-md-12">
	<p class="lead bg-success">
		{if $search_results_count == 1}
		Es wurde {$search_results_count} Dokument gefunden.
		{else}
		Es wurden {$search_results_count} Dokumente gefunden.
		{/if}
	</p>
	<p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Vorlage-Nr.</th>
                    <th>Betreff</th>
                    <th>Datum</th>
                    <th>Art</th>
                    <th class="invisible">Details</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$search_results_data item=dataset}
				<tr>
					<td>
                        {$dataset->getName()}
                    </td>
                    <td>
                        {$dataset->getSubject()}
                    </td>
                    <td>
                        {$dataset->getDate()|date_format:"%d.%m.%Y"}
                    </td>
                    <td>
                        {$dataset->getType()}
                    </td>
                    <td>
                        <a class="btn btn-default" href="/vorlagen/{$dataset->getId()}">Details anzeigen</a>
                    </td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>
{/if}