<?php

namespace App\Helpers\Classes\Cron;

class CronJob
{
  private $minutes = '*';
  private $hours = '*';
  private $days = '*';
  private $months = '*';
  private $weekdays = '*';
  private $command;

  #

  public function __construct(string $job = null)
  {
    if ($job) {
      $job = explode(" ", $job, 6);
      $this
        ->minutes($job[0] ?? null)
        ->hours($job[1] ?? null)
        ->days($job[2] ?? null)
        ->months($job[3] ?? null)
        ->weekdays($job[4] ?? null)
        ->command($job[5] ?? null);
    }
  }

  #

  public function minutes($minutes)
  {
    $this->minutes = $minutes;
    return $this;
  }

  public function hours($hours)
  {
    $this->hours = $hours;
    return $this;
  }

  public function days($days)
  {
    $this->days = $days;
    return $this;
  }

  public function months($months)
  {
    $this->months = $months;
    return $this;
  }

  public function weekdays($weekdays)
  {
    $this->weekdays = $weekdays;
    return $this;
  }

  public function command($command)
  {
    $this->command = str_replace(' >/dev/null 2>&1', '', $command);
    return $this;
  }

  #

  public function getMinutes()
  {
    return $this->minutes;
  }

  public function getHours()
  {
    return $this->hours;
  }

  public function getDays()
  {
    return $this->days;
  }

  public function getMonths()
  {
    return $this->months;
  }

  public function getWeekdays()
  {
    return $this->weekdays;
  }

  public function getCommand()
  {
    return $this->command;
  }

  #

  public function getLine()
  {
    return "{$this->minutes} {$this->hours} {$this->days} {$this->months} {$this->weekdays} {$this->command} >/dev/null 2>&1";
  }
}
