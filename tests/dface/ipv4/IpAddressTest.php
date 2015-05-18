<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

class IpAddressTest extends \PHPUnit_Framework_TestCase {

	protected function getTestScope(){
		return ['1.1.1.1', '127.0.0.1', '10.10.10.1', '194.50.85.1', '192.168.0.6', '255.255.255.1'];
	}

	/**
	 * @return IpAddress[]
	 */
	protected function getTestAddresses(){
		$addresses = [];
		foreach($this->getTestScope() as $ip_str){
			$addresses[$ip_str] = IpAddress::fromString($ip_str);
		}
		return $addresses;
	}

	function testFromString(){
		foreach($this->getTestAddresses() as $ip_str=>$ip){
			$this->assertEquals($ip->toString(), $ip_str);
		}
	}

	function testFromLong(){
		foreach($this->getTestScope() as $ip_str){
			$long = ip2long($ip_str);
			$ip = new IpAddress($long);
			$this->assertEquals($ip->toLong(), $long);
		}
	}

	function testStringSourceException(){
		$this->setExpectedException(\InvalidArgumentException::class);
		new IpAddress("1.1");
	}

	function testBadFormatException(){
		$this->setExpectedException(\InvalidArgumentException::class);
		IpAddress::fromString('1');
	}

	function testToUnsigned(){
		$ip = IpAddress::fromString('255.255.255.1');
		$this->assertEquals(0xffffff01, $ip->toUnsigned());
	}

	function testSort(){
		$list = $this->getTestAddresses();
		IpAddress::sort($list);
		$ip_sorted = implode(" ", IpAddress::toStringList($list));
		$this->assertEquals("1.1.1.1 10.10.10.1 127.0.0.1 192.168.0.6 194.50.85.1 255.255.255.1", $ip_sorted);
	}

}
