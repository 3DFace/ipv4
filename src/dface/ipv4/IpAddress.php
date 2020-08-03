<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

class IpAddress
{

	private int $address;

	public function __construct(int $address_int)
	{
		$this->address = $address_int;
	}

	public function toLong() : int
	{
		return $this->address;
	}

	public function __toString() : string
	{
		return \long2ip($this->address);
	}

	public function toUnsigned() : string
	{
		return \sprintf('%u', $this->address);
	}

	public static function fromString(string $ip_str) : self
	{
		$long = \ip2long($ip_str);
		if ($long === false) {
			throw new \InvalidArgumentException("Bad address format: $ip_str");
		}
		return new IpAddress($long);
	}

	public static function compare(IpAddress $i1, IpAddress $i2) : int
	{
		$a1 = $i1->address;
		$a2 = $i2->address;
		if ($a1 === $a2) {
			return 0;
		}
		$a1 = (float)\sprintf('%u', $i1->address);
		$a2 = (float)\sprintf('%u', $i2->address);
		return $a1 > $a2 ? 1 : -1;
	}

	/**
	 * @param IpAddress[] $addresses
	 */
	public static function sort(array &$addresses) : void
	{
		\usort($addresses, [self::class, 'compare']);
	}

	/**
	 * @param IpAddress[] $addresses
	 * @return string[]
	 */
	public static function toStringList(array $addresses) : array
	{
		$list = [];
		foreach ($addresses as $ip) {
			$list[] = (string)$ip;
		}
		return $list;
	}

}
