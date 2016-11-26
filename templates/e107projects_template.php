<?php

/**
 * @file
 * Templates.
 */

$E107PROJECTS_TEMPLATE['openlayers_menu'] = '
<div id="commitMap" class="commit-map"></div>
<div id="commitMapOverlay" class="commit-map-overlay"></div>
';

$E107PROJECTS_TEMPLATE['summary_menu'] = '
<div class="senary-container">
	<div class="container">
		<div class="row">
   			<div class="col-sm-12 text-center">
   			    <h2>e107 is powered by an open source community</h2>
			</div>
   		</div>
	</div>
</div>

<div class="summary-container">
	<div class="container">
		<div class="row">
   			<div class="col-sm-6 col-md-3 col-1">
   			    <div class="col-inner">
   			        {SUMMARY_MENU_COL_1}
   			    </div>
			</div>
			<div class="col-sm-6 col-md-3 col-2">
				<div class="col-inner">
   			        {SUMMARY_MENU_COL_2}
   			    </div>
			</div>
			<div class="col-sm-6 col-md-3 col-3">
				<div class="col-inner">
   			        {SUMMARY_MENU_COL_3}
   			    </div>
			</div>
			<div class="col-sm-6 col-md-3 col-4">
				<div class="col-inner">
   			        {SUMMARY_MENU_COL_4}
   			    </div>
			</div>
   		</div>
	</div>
</div>
';

$E107PROJECTS_TEMPLATE['projects']['list']['pre'] = '
<table class="table table-hover search-results">
	<thead>
		<tr>
			<th><a>{PROJECT_NAME_LABEL}</a></th>
			<th width="15%" class="hidden-xs"><a>{PROJECT_OWNER_LABEL}</a></th>
			<th width="15%" class="hidden-xs"><a>{PROJECT_STARS_LABEL}</a></th>
		</tr>
	</thead>
	<tbody>
';

$E107PROJECTS_TEMPLATE['projects']['list']['row'] = '
		<tr>
			<td class="name">
				<h4>
					<a href="{PROJECT_URL}" rel="noopener noreferrer">
						{PROJECT_NAME}
					</a>
				</h4>
				<small>
					<a href="{PROJECT_URL}" rel="noopener noreferrer">
						{PROJECT_URL}
					</a>
				</small>
				<p class="description">{PROJECT_DESCRIPTION}</p>
				<p class="visible-xs">
					<span class="label">{PROJECT_OWNER_LABEL}: <span>{PROJECT_OWNER}</span></span>
					<span class="label">{PROJECT_STARS_LABEL}: <span>{PROJECT_STARS}</span></span>
				</p>
			</td>
			<td class="hidden-xs owner">{PROJECT_OWNER}</td>
			<td class="hidden-xs stars">{PROJECT_STARS}</td>
		</tr>
';

$E107PROJECTS_TEMPLATE['projects']['list']['post'] = '
	</tbody>
</table>
';

$E107PROJECTS_TEMPLATE['submit']['empty'] = '
<p class="lead text-center">{SUBMIT_PROJECT_EMPTY_TEXT}</p>
';

$E107PROJECTS_TEMPLATE['submit']['pre'] = '
<div class="help-block">
	{SUBMIT_PROJECT_HELP_TEXT}
</div>
<table class="table table-hover">
	<thead>
		<tr>
			<th>{SUBMIT_PROJECT_NAME_LABEL}</th>
			<th width="15%" class="text-center">{SUBMIT_PROJECT_ACTION_LABEL}</th>
		</tr>
	</thead>
	<tbody>
';

$E107PROJECTS_TEMPLATE['submit']['row'] = '
		<tr>
			<td>
				<p class="lead">{SUBMIT_PROJECT_NAME}</p>
				<small>{SUBMIT_PROJECT_DESCRIPTION}</small>
			</td>
			<td class="text-center">
				{SUBMIT_PROJECT_ACTION}
			</td>
		</tr>
';

$E107PROJECTS_TEMPLATE['submit']['post'] = '
	</tbody>
</table>
';
