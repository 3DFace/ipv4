<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

use PHPUnit\Framework\TestCase;

class IpAddressTest extends TestCase {

	protected function getTestScope() : array
	{
		return ['1.1.1.1', '127.0.0.1', '10.10.10.1', '194.50.85.1', '192.168.0.6', '255.255.255.1'];
	}

	/**
	 * @return IpAddress[]
	 */
	protected function getTestAddresses() : array
	{
		$addresses = [];
		foreach($this->getTestScope() as $ip_str){
			$addresses[$ip_str] = IpAddress::fromString($ip_str);
		}
		return $addresses;
	}

	public function testFromString() : void
	{
		foreach($this->getTestAddresses() as $ip_str=>$ip){
			self::assertEquals((string)$ip, $ip_str);
		}
	}

	public function testFromLong() : void
	{
		foreach($this->getTestScope() as $ip_str){
			$long = ip2long($ip_str);
			$ip = new IpAddress($long);
			self::assertEquals($ip->toLong(), $long);
		}
	}

	public function testBadFormatException() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		IpAddress::fromString('1');
	}

	public function testToUnsigned() : void
	{
		$ip = IpAddress::fromString('255.255.255.1');
		self::assertEquals(0xffffff01, $ip->toUnsigned());
	}

	public function testSort() : void
	{
		$list = $this->getTestAddresses();
		IpAddress::sort($list);
		$ip_sorted = implode(" ", IpAddress::toStringList($list));
		self::assertEquals("1.1.1.1 10.10.10.1 127.0.0.1 192.168.0.6 194.50.85.1 255.255.255.1", $ip_sorted);
	}

}
