<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

class IpSubnet
{

	private int $address;
	private int $mask;

	public function __construct(int $address_int, int $mask_int)
	{
		$this->address = $address_int;
		$this->mask = $mask_int;
		if ($this->address !== ($this->address & $this->mask)) {
			throw new \InvalidArgumentException("Invalid address/mask combination");
		}
	}

	public function containsSubnet(IpSubnet $subnet) : bool
	{
		return ($this->mask < $subnet->mask) && (($subnet->address & $this->mask) === $this->address);
	}

	public function containsAddress(IpAddress $ip) : bool
	{
		$long = $ip->toLong();
		return (($long & $this->mask) === ($this->address));
	}

	public function getBroadcastAddress() : IpAddress
	{
		return new IpAddress(~$this->mask | $this->address);
	}

	public function __toString() : string
	{
		return \long2ip($this->address).'/'.\long2ip($this->mask);
	}

	public function toShortString() : string
	{
		$bin = \decbin($this->mask);
		$m = 0;
		while ($m < 32 && $bin[$m] === '1') {
			$m++;
		}
		return \long2ip($this->address).'/'.$m;
	}

	public static function fromString(string $subnet_str) : self
	{
		$arr = \explode("/", $subnet_str);
		$count = \count($arr);
		if ($count === 2) {
			$mask = $arr[1];
			if (\is_numeric($mask)) {
				// 10.10.10.0/24
				if ($mask < 0 || $mask > 32) {
					throw new \InvalidArgumentException("CIDR mask value must be in [0-32] range: $arr[1]");
				}
				$mask = \bindec(\str_repeat("1", $mask).\str_repeat("0", 32 - $mask));
			}else {
				// 10.10.10.0/255.255.255.0
				$mask = \ip2long($mask);
				if ($mask === false) {
					throw new \InvalidArgumentException("Invalid mask format: $arr[1]");
				}
			}
		}elseif ($count === 1) {
			$mask = 0xffffffff;
		}else {
			throw new \InvalidArgumentException("Invalid subnet format");
		}
		$address = \ip2long($arr[0]);
		if ($address === false) {
			throw new \InvalidArgumentException("Invalid address format: $arr[0]");
		}
		return new IpSubnet($address, $mask);
	}

}
