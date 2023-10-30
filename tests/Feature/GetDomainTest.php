<?php
namespace Peter\WebsiteInfo\Tests;

use Orchestra\Testbench\TestCase;
use Peter\WebsiteInfo\CheckDomain;

class GetDomainTest extends TestCase
{
    /**
     * @test
     */
    public function get_domain_test()
    {
        $input_domain = 'https://www.allrecipes.com/';
        $expected_return = 'allrecipes.com';
        $checkDomain = new CheckDomain($input_domain);
        $result_return = $checkDomain->getDomain();
        $this->assertSame($expected_return, $result_return);
    }

    /**
     * @test
     */
    public function trim_test()
    {
        $input_domain = 'https://www.allrecipes.com/';
        $expected_return = 'allrecipes.com';
        $checkDomain = new CheckDomain($input_domain);
        $result_return = $checkDomain->trimDomain($input_domain);
        $this->assertSame($expected_return, $result_return);
    }

    /**
     * @test
     */
    public function check_tld_test()
    {
        $input_domain = 'allrecipes.com';
        $expected_return = 'TRUE';
        $checkDomain = new CheckDomain($input_domain);
        $result_return = $checkDomain->check_tld($input_domain);
        // dd($expected_return, $result_return);
        $this->assertSame($expected_return, $result_return);
    }

    /**
     * @test
     * Test Success Post Method
     */
    public function success_get_request_test()
    {
        $input_url = 'https://www.allrecipes.com'; 
        $input_path = '/gallery/ground-beef-casseroles/';
        $input_port = 80; //Later
        $input_method = 'GET'; //Later
        $input_query = []; //Later
        
        $return_response = CheckDomain::curl_request($input_url, $input_path, $input_method);
        // dd($return_response['http_code']);
        $this->assertNotEmpty($return_response['response']);
        $this->assertSame(200, $return_response['http_code']);
    }

    /**
     * @test
     * Test Fail Post Method
     */
    public function fail_get_request_test()
    {
        $input_url = 'https://www.example.com'; 
        $input_path = '/test/supertest';
        $input_port = 80; //Later
        $input_method = 'GET'; //Later
        $input_query = []; //Later
        
        $return_response = CheckDomain::curl_request($input_url, $input_path, $input_method);
        // dd($return_response['http_code']);
        $this->assertNotEmpty($return_response['response']);
        $this->assertSame(404, $return_response['http_code']);
    }
}
