{if $file_error_empty_parameter != ''}
    <p class="lead bg-danger">{$file_error_empty_parameter}</p>
{elseif $file_error_wrong_id != ''}
    <p class="lead bg-danger">{$file_error_wrong_id}</p>
{elseif $file_show_form == false}
    <h2>{$file->getFilename()}</h2>
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
{elseif $file_show_form == true}
    <div class="col-md-9">
        {if $search_error_input_empty == 'has-error'}
       {literal}
       <script>
           $(document ).ready(function() {
               $( "#errormessage" ).effect('shake', {direction: "ltr", times: 4, distance: 5}, 1000);
           });
       </script>
        <p class="text-danger" id="errormessage">Bitte ein Wort oder eine Wortgruppe in das Suchfeld eingeben</p>
       {/literal}
       	{/if}
       	<form role="form" action="" method="post">
       		<div class="form-group {$search_error_input_empty}">
       			<input name="s" type="text" class="form-control input-lg" id="s" placeholder="Wort oder Wortgruppe" {if $search_input_s != ""}value="{$search_input_s}"{/if} />
       	  	</div>
       		<div class="checkbox">
       		  <label>
       		    <input type="checkbox" value="true" {if $filter_remove_einladungen == true}checked="checked"{/if} name="filter_remove_einladungen" />
       		    Einladungen ausschließen
       		  </label>
       		</div>
               <div class="checkbox">
             		  <label>
             		    <input type="checkbox" value="true" {if $filter_reduce_niederschriften == true}checked="checked"{/if} name="filter_reduce_niederschriften" />
             		    Nur Niederschriften anzeigen
             		  </label>
             		</div>
       		<input type="submit" class="btn btn-block btn-primary btn-sm" value="Start" />
       	</form>
    </div>
    <div class="col-md-3" id="bigteaser">
    	<p class="lead">Recherche in Dokumenten</p>
        <p>Lorem ipsum dolor</p>
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
					<th>Datei</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$search_results_data item=dataset}
				<tr>
					<td>
						{literal}
						<script type='text/javascript'>
							$(document).ready(function()
							{
								$('#myModal{/literal}{$dataset->getId()}{literal}').on('show.bs.modal', function (e)
								{
									$('a#modal-body-{/literal}{$dataset->getId()}{literal}').media({width:850, height:650});
								});
							});
						</script>
						{/literal}
                        <p class="text-muted">{$dataset->getFilename()}&nbsp;&ndash;&nbsp;Trefferwert: {$dataset->getScore()}</p>
						<button class="btn btn-default" data-toggle="modal" data-target="#myModal{$dataset->getId()}" title="{$dataset->getFilename()}">Dokument ansehen</button>
						<div class="modal fade bs-example-modal-lg" id="myModal{$dataset->getId()}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					        <div class="modal-dialog modal-lg">
					            <div class="modal-content">
					                <div class="modal-header">
					                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					                    <h4 class="modal-title" id="myModalLabel">{$dataset->getFilename()}</h4>
					                </div>
                                    <div class="modal-body">
					                    <a id="modal-body-{$dataset->getId()}" class="media" href="/downloads/{$dataset->getFilename()}"></a>
							            <a href="/downloads/{$dataset->getFilename()}">Download der Datei</a>
					                </div>
					                <div class="modal-footer">
					                    <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
					                </div>
					            </div>
					        </div>
					    </div>
				    	<a class="btn btn-default" href="/dokumente/{$dataset->getId()}">Dokumentdetails anzeigen</a>
					    <a class="btn btn-default" href="/downloads/{$dataset->getFilename()}">Download ({$dataset->getSize()})</a>
                    </td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>
{/if}