<?php

add_filter ('bps_fields_setup', 'bps_xprofile_setup');
function bps_xprofile_setup ($fields)
{
	global $group, $field;

	if (!function_exists ('bp_has_profile'))
	{
		printf ('<p class="bps_error">'. __('%s: The BuddyPress Extended Profiles component is not active.', 'bp-profile-search'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>');
		return $fields;
	}

	$args = array ('hide_empty_fields' => false, 'member_type' => bp_get_member_types ());
	if (bp_has_profile ($args))
	{
		while (bp_profile_groups ())
		{
			bp_the_profile_group ();
			$group_name = str_replace ('&amp;', '&', stripslashes ($group->name));

			while (bp_profile_fields ())
			{
				bp_the_profile_field ();
				$f = new stdClass;

				$f->group = $group_name;
				$f->id = $field->id;
				$f->code = 'field_'. $field->id;
				$f->name = str_replace ('&amp;', '&', stripslashes ($field->name));
				$f->name = bps_wpml (0, $f->id, 'name', $f->name);
				$f->description = str_replace ('&amp;', '&', stripslashes ($field->description));
				$f->description = bps_wpml (0, $f->id, 'description', $f->description);
				$f->type = $field->type;
				$f->options = bps_xprofile_options ($field->id);
				foreach ($f->options as $key => $label)
					$f->options[$key] = bps_wpml (0, $f->id, 'option', $label);

				$f->filters = bps_xprofile_filters ($field->type);
				$f->display = empty ($f->filters)? '': $field->type;
				$f->search = 'bps_xprofile_search';

				if ($field->type != 'checkbox' && $field->type != 'multiselectbox')
				{
					$f->sort_directory = 'bps_xprofile_sort_directory';
					$f->get_value = 'bps_xprofile_get_value';
				}

				$fields[] = $f;
			}
		}
	}

	return $fields;
}

function bps_xprofile_search ($f)
{
	global $bp, $wpdb;

	$value = $f->value;
	$filter = bps_filterXquery ($f);

	$sql = array ('select' => '', 'where' => array ());

	$sql['select'] = "SELECT user_id FROM {$bp->profile->table_name_data}";
	$sql['where']['field_id'] = $wpdb->prepare ("field_id = %d", $f->id);

	switch ($filter)
	{
	case 'range':
		$min = $f->min;
		$max = $f->max;

		if ($min !== '')  $sql['where']['min'] = $wpdb->prepare ("value >= %f", $min);
		if ($max !== '')  $sql['where']['max'] = $wpdb->prepare ("value <= %f", $max);
		break;

	case 'age_range':
		$min = $f->min;
		$max = $f->max;
		$time = time ();
		$day = date ("j", $time);
		$month = date ("n", $time);
		$year = date ("Y", $time);
		$ymin = $year - $max - 1;
		$ymax = $year - $min;

		if ($max !== '')  $sql['where']['age_min'] = $wpdb->prepare ("DATE(value) > %s", "$ymin-$month-$day");
		if ($min !== '')  $sql['where']['age_max'] = $wpdb->prepare ("DATE(value) <= %s", "$ymax-$month-$day");
		break;

	case 'contains':
		$value = str_replace ('&', '&amp;', $value);
		$escaped = '%'. bps_esc_like ($value). '%';
		$sql['where'][$filter] = $wpdb->prepare ("value LIKE %s", $escaped);
		break;

	case 'like':
		$value = str_replace ('&', '&amp;', $value);
		$value = str_replace ('\\\\%', '\\%', $value);
		$value = str_replace ('\\\\_', '\\_', $value);
		$sql['where'][$filter] = $wpdb->prepare ("value LIKE %s", $value);
		break;

	case '':
		$value = str_replace ('&', '&amp;', $value);
		$sql['where'][$filter] = $wpdb->prepare ("value = %s", $value);
		break;

	case 'num':
		$sql['where'][$filter] = $wpdb->prepare ("value = %f", $value);
		break;

	case 'is_in':
		$values = (array)$value;
		$parts = array ();
		foreach ($values as $value)
		{
			$value = str_replace ('&', '&amp;', $value);
			$parts[] = $wpdb->prepare ("value = %s", $value);
		}
		$sql['where'][$filter] = '('. implode (' OR ', $parts). ')';
		break;

	case 'match_any':
	case 'match_all':
		$values = (array)$value;
		$parts = array ();
		foreach ($values as $value)
		{
			$value = str_replace ('&', '&amp;', $value);
			$escaped = '%:"'. bps_esc_like ($value). '";%';
			$parts[] = $wpdb->prepare ("value LIKE %s", $escaped);
		}
		$match = ($filter == 'match_any')? ' OR ': ' AND ';
		$sql['where'][$filter] = '('. implode ($match, $parts). ')';
		break;

	default:
		return array ();
	}

	$sql = apply_filters ('bps_field_sql', $sql, $f);
	$query = $sql['select']. ' WHERE '. implode (' AND ', $sql['where']);

	$results = $wpdb->get_col ($query);
	return $results;
}

function bps_xprofile_sort_directory ($sql, $object, $f, $order)
{
	global $bp, $wpdb;

	$object->uid_name = 'user_id';
	$object->uid_table = $bp->profile->table_name_data;

	$sql['select'] = "SELECT u.user_id AS id FROM {$object->uid_table} u";
	$sql['where'] = str_replace ('u.ID', 'u.user_id', $sql['where']);
	$sql['where'][] = "u.user_id IN (SELECT ID FROM {$wpdb->users} WHERE user_status = 0)";
	$sql['where'][] = $wpdb->prepare ("u.field_id = %d", $f->id);
	$sql['orderby'] = "ORDER BY u.value";
	$sql['order'] = $order;

	return $sql;
}

function bps_xprofile_get_value ($f)
{
	global $members_template;

	if ($members_template->current_member == 0)
	{
		$users = wp_list_pluck ($members_template->members, 'ID');
		BP_XProfile_ProfileData::get_value_byid ($f->id, $users);
	}

	return BP_XProfile_ProfileData::get_value_byid ($f->id, $members_template->member->ID);
}

function bps_xprofile_options ($id)
{
	static $options = array ();

	if (isset ($options[$id]))  return $options[$id];

	$field = new BP_XProfile_Field ($id);
	if (empty ($field->id))  return array ();

	$options[$id] = array ();
	$rows = $field->get_children ();
	if (is_array ($rows))
		foreach ($rows as $row)
			$options[$id][stripslashes (trim ($row->name))] = stripslashes (trim ($row->name));

	return $options[$id];
}

function bps_xprofile_filters ($type)
{
	$filters = array
	(
		'textbox'			=> array ('' => 'default', 'range' => 'range'),
		'number'			=> array ('' => 'default', 'range' => 'range'),
		'url'				=> array ('' => 'default'),
		'textarea'			=> array ('' => 'default'),
		'selectbox'			=> array ('' => 'default', 'range' => 'range'),
		'radio'				=> array ('' => 'default', 'range' => 'range'),
		'multiselectbox'	=> array ('' => 'default'),
		'checkbox'			=> array ('' => 'default'),
		'datebox'			=> array ('range' => 'range'),
	);

	if (isset ($filters[$type]))  return $filters[$type];
	return array ();
}

function bps_filtersXvalidation ($f)
{
	$type = apply_filters ('bps_field_validation_type', $f->type, $f);
	$type = apply_filters ('bps_field_type_for_validation', $type, $f);

	return bps_xprofile_filters ($type);
}

function bps_filterXquery ($f)
{
	$type = apply_filters ('bps_field_query_type', $f->type, $f);
	$type = apply_filters ('bps_field_type_for_query', $type, $f);
	
	if ($f->filter == 'range')
		return ($type == 'datebox')? 'age_range': 'range';

	switch ($type)
	{
	case 'textbox':
	case 'textarea':
	case 'url':
		return bps_text_search ();

	case 'number':
		return 'num';

	case 'selectbox':
	case 'radio':
		return 'is_in';

	case 'multiselectbox':
	case 'checkbox':
		$all = apply_filters ('bps_field_checkbox_match_all', false, $f->id);
		return $all? 'match_all': 'match_any';
	}

	return false;
}

function bps_displayXsearch_form ($f)
{
	$type = apply_filters ('bps_field_type_for_filters', $f->type, $f);
	$type = apply_filters ('bps_field_type_for_search_form', $type, $f);

	return $type;
}

add_filter ('bps_fields_setup', 'bps_anyfield_setup', 99);
function bps_anyfield_setup ($fields)
{
	$f = new stdClass;

	$f->group = __('Other', 'bp-profile-search');
//	$f->id = 'any';
	$f->code = 'field_any';
	$f->name = __('Any field', 'bp-profile-search');
	$f->description = __('Search every BP Profile Field', 'bp-profile-search');
	$f->type = 'anyfield';
	$f->options = array ();
	$f->filters = array ('' => 'default');
	$f->display = 'textbox';
	$f->search = 'bps_anyfield_search';

	$fields[] = $f;
	return $fields;
}

function bps_anyfield_search ($f)
{
	global $bp, $wpdb;

	$value = str_replace ('&', '&amp;', $f->value);
	$escaped = '%'. bps_esc_like ($value). '%';

	$sql = array ('select' => '', 'where' => array ());
	$sql['select'] = "SELECT DISTINCT user_id FROM {$bp->profile->table_name_data}";
	$sql['where'][$f->filter] = $wpdb->prepare ("value LIKE %s", $escaped);

	$sql = apply_filters ('bps_field_sql', $sql, $f);
	$query = $sql['select']. ' WHERE '. implode (' AND ', $sql['where']);

	$results = $wpdb->get_col ($query);
	return $results;
}

add_filter ('bps_fields_setup', 'bps_membertype_setup');
function bps_membertype_setup ($fields)
{
	global $wpdb;

	$f = new stdClass;

	$f->group = __('Other', 'bp-profile-search');
//	$f->id = 'type';
	$f->code = 'membertype';
	$f->name = __('Member type', 'bp-profile-search');
	$f->description = __('Select the member type', 'bp-profile-search');
	$f->type = 'membertype';

	$f->options = array ();
	$member_types = bp_get_member_types (array (), 'objects');
	foreach ($member_types as $type)
	{
		$label = $type->labels['singular_name'];
		$f->options[$label] = $label;
	}

	$f->filters = array ('' => 'default');
	$f->display = 'selectbox';
	$f->search = 'bps_membertype_search';

	$fields[] = $f;
	return $fields;
}

function bps_membertype_search ($f)
{
	global $wpdb;

	$types = array ();
	$values = stripslashes_deep ($f->values);
	$member_types = bp_get_member_types (array (), 'objects');

	foreach ($values as $value)
		foreach ($member_types as $type)
			if ($value == $type->labels['singular_name'])  { $types[] = $type->name;  break; }

	$sql = array ('select' => '', 'where' => array ());
	$sql['select'] = "SELECT object_id FROM {$wpdb->base_prefix}term_relationships";
	$sql['where'][$f->filter] = "term_taxonomy_id IN (
		SELECT term_taxonomy_id
		FROM {$wpdb->base_prefix}term_taxonomy
		INNER JOIN {$wpdb->base_prefix}terms USING (term_id)
		WHERE taxonomy = 'bp_member_type'
		AND name IN ('". implode ("','", $types). "'))";

	$sql = apply_filters ('bps_field_sql', $sql, $f);
	$query = $sql['select']. ' WHERE '. implode (' AND ', $sql['where']);

	$results = $wpdb->get_col ($query);
	return $results;
}
