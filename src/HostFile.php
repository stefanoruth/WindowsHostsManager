<?php

namespace Kodo\WindowsHosts;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class HostFile
{
	public static $filepath = 'C:\Windows\System32\drivers\etc\hosts';
	protected $hosts = [];

	public function __construct($hosts = [])
	{
		$this->hosts = $hosts;
	}

	public static function load()
	{
		$data = file_get_contents(static::$filepath);

        preg_match_all("/(#?)(?:\s+)?([\d\.\:]+)\s+((?:[\w\.]+\.[\w]{1,3})|(?:localhost))/i", $data, $matches);

        $hosts = [];
        foreach (range(0, count($matches[1])-1) as $i) {
        	$hosts[$i] = [
        		$i,
        		$matches[2][$i], // Ip
        		$matches[3][$i], // Domain
        		$matches[1][$i] != '#' ? 'Active' : 'Disabled', // Status
        	];
        }

        return new static($hosts);
	}

	public function add($ip, $domain)
	{
		$ip = filter_var($ip, FILTER_VALIDATE_IP);

		if ($ip == false) {
			throw new \InvalidArgumentException("Invalid IP address");	
		}

		$this->hosts[] = [count($this->hosts), $ip, $domain, 'Active'];

		return $this;
	}

	public function remove($id)
	{
		if (!array_key_exists($id, $this->hosts)) {
			throw new \Exception("Host does not exist.");
		}

		unset($this->hosts[$id]);

		return $this;
	}

	public function toggle($id)
	{
		if (!array_key_exists($id, $this->hosts)) {
			throw new \Exception("Host does not exist.");
		}

		$this->hosts[$id][3] = $this->hosts[$id][3] == 'Active' ? 'Disabled' : 'Active';

		return $this;
	}

	public function save()
	{
		$string = "";

		foreach ($this->hosts as $host) {
			$status = $host[3] == 'Disabled' ? '# ' : '';
			$string .= "{$status}{$host[1]} {$host[2]}\n";
		}

		file_put_contents(static::$filepath, $string);

		return $this;
	}

	public function show(OutputInterface $output)
	{
		$table = new Table($output);
        $table->setHeaders(['#', 'Ip', 'Domain', 'Status'])
        	  ->setRows($this->hosts)
              ->render();
	}
}