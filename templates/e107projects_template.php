<?php

/**
 * @file
 * Templates.
 */

$E107PROJECTS_TEMPLATE['openlayers_menu'] = '
<div id="commitMap" class="commit-map">
	<div id="commitMapPopup" class="ol-popup">
		<div id="popup-content"></div>
	</div>
</div>
<!-- div id="commitMapOverlay" class="commit-map-overlay"></div -->
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
			<div class="col-sm-12 col-md-4 col-2">
				<div class="col-inner">
   			        {SUMMARY_MENU_COL_2}
   			    </div>
			</div>
   			<div class="col-sm-12 col-md-4 col-1">
   			    <div class="col-inner">
   			        {SUMMARY_MENU_COL_1}
   			    </div>
			</div>
			<div class="col-sm-12 col-md-4 col-3">
				<div class="col-inner">
   			        {SUMMARY_MENU_COL_3}
   			    </div>
			</div>
   		</div>
	</div>
</div>
';

$E107PROJECTS_TEMPLATE['projects']['list']['search'] = '
{PROJECT_LIST_SEARCH}
';

$E107PROJECTS_TEMPLATE['projects']['list']['pre'] = '
<table class="table table-hover search-results">
	<thead>
		<tr>
			<th>{PROJECT_LIST_PROJECT_NAME_LABEL}</th>
			<th width="15%" class="hidden-xs text-center">{PROJECT_LIST_PROJECT_OWNER_LABEL}</th>
			<th width="15%" class="hidden-xs text-center">{PROJECT_LIST_PROJECT_STARS_LABEL}</th>
		</tr>
	</thead>
	<tbody>
';

$E107PROJECTS_TEMPLATE['projects']['list']['row'] = '
		<tr>
			<td class="name">
				<p class="lead">
					<a href="{PROJECT_LIST_PROJECT_URL}" rel="noopener noreferrer">
						{PROJECT_LIST_PROJECT_NAME}
					</a>
				</p>
				<small>{PROJECT_LIST_PROJECT_DESCRIPTION}</small>
				<p class="visible-xs">
					<span class="label">{PROJECT_LIST_PROJECT_OWNER_LABEL}: <span>{PROJECT_LIST_PROJECT_OWNER}</span></span>
					<span class="label">{PROJECT_LIST_PROJECT_STARS_LABEL}: <span>{PROJECT_LIST_PROJECT_STARS}</span></span>
				</p>
			</td>
			<td class="hidden-xs owner text-center">{PROJECT_LIST_PROJECT_OWNER}</td>
			<td class="hidden-xs stars text-center">{PROJECT_LIST_PROJECT_STARS}</td>
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

$E107PROJECTS_TEMPLATE['notification'] = '
<div class="notification-container">
  <div class="left-container">
    {NOTIFICATION_AVATAR}
  </div>
  <div class="right-container">
    <div class="message">
        {NOTIFICATION_MESSAGE}
    </div>
    <div class="link">
      {NOTIFICATION_LINK}
    </div>
  </div>
  <div class="clear clearfix"></div>
</div>
';

$E107PROJECTS_TEMPLATE['project'] = '
<div class="panel panel-default">
	<div class="panel-body">
		<strong>{PROJECT_DESCRIPTION}</strong>
	</div>
</div>
<div class="project-readme-container">
	{PROJECT_README}
</div>
';
