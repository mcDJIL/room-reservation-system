<?php

function esc($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_date_id($value)
{
  if (empty($value)) {
    return '-';
  }

  $timestamp = strtotime($value);
  if ($timestamp === false) {
    return esc($value);
  }

  return date('d M Y', $timestamp);
}

function status_label($status)
{
  if ($status === 'approved') {
    return 'Disetujui';
  }

  if ($status === 'rejected') {
    return 'Ditolak';
  }

  if ($status === 'cancelled') {
    return 'Dibatalkan';
  }

  return 'Menunggu';
}

function status_class($status)
{
  if ($status === 'approved') {
    return 'status-approved';
  }

  if ($status === 'rejected') {
    return 'status-rejected';
  }

  if ($status === 'cancelled') {
    return 'status-cancelled';
  }

  return 'status-pending';
}

function build_history_query($page, $search_term, $filter_date, $filter_status)
{
  $params = [];

  if ($search_term !== '') {
    $params['q'] = $search_term;
  }

  if ($filter_date !== '') {
    $params['date'] = $filter_date;
  }

  if ($filter_status !== '') {
    $params['status'] = $filter_status;
  }

  $params['page'] = $page;

  return '?' . http_build_query($params);
}