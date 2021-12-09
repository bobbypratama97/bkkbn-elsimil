<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class LogbookHistory extends Model
{
  protected $table = 'logbook_history';
  protected $fillable = ['id', 'user_id', 'member_id', 'log_type', 'meta_data'];

  /* LOG TYPE
    1 = INTERVENSI
    2 = PENGISIAN KUESIONER
  */
  public function addToLogbook($user_id, $member_id, $log_type, $meta_data)
  {
    $this->user_id = $user_id;
    $this->member_id = $member_id;
    $this->log_type = $log_type;
    $this->meta_data = $meta_data;
    $this->save();
  }
}