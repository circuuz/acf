<?php

/*
 * This file is part of WordPlate.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace WordPlate\Tests;

use PHPUnit\Framework\TestCase;

/**
 * This is the helpers test class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class HelpersTest extends TestCase
{
    protected function assertFieldType($type, $settings)
    {
        $this->assertSame($type, $settings['type']);
    }

    public function testFields()
    {
        $settings = ['name' => 'test', 'label' => 'test'];

        $this->assertFieldType('checkbox', acf_checkbox($settings));
        $this->assertFieldType('email', acf_email($settings));
        $this->assertFieldType('file', acf_file($settings));
        $this->assertFieldType('gallery', acf_gallery($settings));
        $this->assertFieldType('image', acf_image($settings));
        $this->assertFieldType('number', acf_number($settings));
        $this->assertFieldType('oembed', acf_oembed($settings));
        $this->assertFieldType('page_link', acf_page_link($settings));
        $this->assertFieldType('password', acf_password($settings));
        $this->assertFieldType('post_object', acf_post_object($settings));
        $this->assertFieldType('radio', acf_radio($settings));
        $this->assertFieldType('relationship', acf_relationship($settings));
        $this->assertFieldType('select', acf_select($settings));
        $this->assertFieldType('taxonomy', acf_taxonomy($settings));
        $this->assertFieldType('text', acf_text($settings));
        $this->assertFieldType('textarea', acf_textarea($settings));
        $this->assertFieldType('true_false', acf_true_false($settings));
        $this->assertFieldType('url', acf_url($settings));
        $this->assertFieldType('user', acf_user($settings));
        $this->assertFieldType('wysiwyg', acf_wysiwyg($settings));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMissingSettingName()
    {
        acf_text(['label']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMissingSettingLabel()
    {
        acf_text(['name']);
    }
}
