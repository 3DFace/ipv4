<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

use PHPUnit\Framework\TestCase;

class IpSubnetTest extends TestCase {

	public function testFromShortString() : void
	{
		$test = ['1.1.1.1/32', '127.0.0.0/8', '10.0.0.0/16', '194.50.85.0/24', '192.168.0.0/24', '0.0.0.0/0'];
		foreach($test as $subnet_str){
			$subnet = IpSubnet::fromString($subnet_str);
			self::assertEquals($subnet_str, $subnet->toShortString());
		}
	}

	public function testFromLongString() : void
	{
		$test = ['1.1.1.0/255.255.255.0', '127.0.0.0/255.0.0.0', '10.0.0.0/255.255.0.0', '194.50.85.0/255.255.255.0', '192.168.0.0/255.255.255.0', '0.0.0.0/0.0.0.0'];
		foreach($test as $subnet_str){
			$subnet = IpSubnet::fromString($subnet_str);
			self::assertEquals($subnet_str, (string)$subnet);
		}
	}

	public function testContainsAddress() : void
	{
		$subnet_str = '10.10.10.0/24';
		$subnet = IpSubnet::fromString($subnet_str);

		$contains = ['10.10.10.1', '10.10.10.10', '10.10.10.255'];
		foreach($contains as $ip_str){
			self::assertTrue($subnet->containsAddress(IpAddress::fromString($ip_str)), "$subnet_str must contain $ip_str");
		}

		$not_contains = ['10.10.11.1', '192.168.0.1', '127.0.0.1'];
		foreach($not_contains as $ip_str){
			self::assertFalse($subnet->containsAddress(IpAddress::fromString($ip_str)), "$subnet_str must not contain $ip_str");
		}
	}

	public function testContainsAll() : void
	{
		$subnet_str = '0.0.0.0/0.0.0.0';
		$subnet = IpSubnet::fromString($subnet_str);
		$contains = ['10.10.10.1', '127.0.0.1', '192.168.1.88'];
		foreach($contains as $ip_str){
			self::assertTrue($subnet->containsAddress(IpAddress::fromString($ip_str)), "$subnet_str must contain $ip_str");
		}
	}

	public function testContainsSubnet() : void
	{
		$subnet_str = '10.0.0.0/16';
		$subnet = IpSubnet::fromString($subnet_str);

		$contains = ['10.0.0.0/24', '10.0.1.0/24', '10.0.0.1'];
		foreach($contains as $sub_str){
			self::assertTrue($subnet->containsSubnet(IpSubnet::fromString($sub_str)), "$subnet_str must contain $sub_str");
		}

		$not_contains = ['10.0.0.0/8', '192.168.0.0/24', '127.0.0.1'];
		foreach($not_contains as $sub_str){
			self::assertFalse($subnet->containsSubnet(IpSubnet::fromString($sub_str)), "$subnet_str must not contain $sub_str");
		}
	}

	public function testBroadCast() : void
	{
		$test = [
			'10.0.0.0/8' => '10.255.255.255',
			'10.0.0.0/24' => '10.0.0.255',
			'10.0.0.0/30' => '10.0.0.3',
		];
		foreach($test as $subnet_str => $broadcast_str){
			$subnet = IpSubnet::fromString($subnet_str);
			self::assertEquals($broadcast_str, (string)$subnet->getBroadcastAddress());
		}
	}

	public function testImplicit32Format() : void
	{
		$subnet_str = '10.10.10.0';
		$subnet = IpSubnet::fromString($subnet_str);
		self::assertEquals($subnet_str.'/32', $subnet->toShortString());
	}

	public function testInvalidFormatFailed1() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10.10');
	}

	public function testInvalidFormatFailed2() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/');
	}

	public function testInvalidFormatFailed3() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/10.10');
	}

	public function testInvalidFormatFailed4() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/10/10');
	}

	public function testBadMaskFailed() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/33');
	}

}
