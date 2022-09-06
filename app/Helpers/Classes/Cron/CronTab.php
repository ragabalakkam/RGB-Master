<?php

namespace App\Helpers\Classes\Cron;

class CronTab
{
  private $mailto;
  private $shell;
  private $jobs;

  public function __construct()
  {
    $tab = array_filter(explode("\n", shell_exec("crontab -l")));

    $this->mailto = substr($tab[0], 8, -1) ?? null;
    $this->shell = substr($tab[1], 7, -1) ?? null;
    $this->jobs = [];

    foreach (array_slice($tab, 2) as $job) {
      $this->jobs[] = new CronJob($job);
    }
  }

  public function addCronJob(CronJob $job)
  {
    $this->jobs[] = $job;
    $this->updateTab();
  }

  public function removeCronJob(CronJob $job)
  {
    $job_line = $job->getLine();
    $this->jobs = array_filter($this->jobs, fn ($j) => $j->getLine() != $job_line);
    $this->updateTab();
  }

  public function mailTo($mail)
  {
    $this->mailto = $mail;
    return $this;
  }

  public function updateTab()
  {
    $contents[] = 'MAILTO="' . $this->mailto . '"';
    $contents[] = 'SHELL="' . $this->shell . '"';
    foreach ($this->jobs as $job) {
      $contents[] = $job->getLine();
    }
    $contents = implode("\n", $contents);

    shell_exec("echo '$contents' > mycron");
    shell_exec("crontab mycron");
    shell_exec("unlink mycron");
  }
}
