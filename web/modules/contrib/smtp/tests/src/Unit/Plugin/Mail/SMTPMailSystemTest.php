<?php

namespace Drupal\Tests\smtp\Unit\Plugin\Mail;

use Drupal\Component\Utility\EmailValidator;
use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystem;
use Drupal\Core\File\MimeType\MimeTypeGuesser;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\smtp\Plugin\Mail\SMTPMailSystem;
use Drupal\Tests\UnitTestCase;
use PHPMailer\PHPMailer\PHPMailer;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Validate requirements for SMTPMailSystem.
 *
 * @group SMTP
 */
class SMTPMailSystemTest extends UnitTestCase {

  private $emailValidator;

  /**
   * Test setup.
   */
  public function setup() {
    $this->mockConfig = $this->prophesize(Config::class);
    $this->mockConfigFactory = $this->prophesize(ConfigFactoryInterface::class);
    $this->mockConfigFactory->get('smtp.settings')->willReturn($this->mockConfig->reveal());
    $this->mockConfigFactory->getEditable('smtp.settings')->willReturn($this->mockConfig->reveal());

    $this->mockLogger = $this->prophesize(LoggerChannelFactoryInterface::class);
    $this->mockLogger->get('smtp')->willReturn($this->prophesize(LoggerChannelInterface::class));
    $this->mockMessenger = $this->prophesize(Messenger::class);
    $this->mockCurrentUser = $this->prophesize(AccountProxy::class);
    $this->mockFileSystem = $this->prophesize(FileSystem::class);
    $this->mimeTypeGuesser = $this->prophesize(MimeTypeGuesser::class);

    $mockContainer = $this->mockContainer = $this->prophesize(ContainerInterface::class);
    $mockContainer->get('config.factory')->willReturn($this->mockConfigFactory->reveal());
    $mockContainer->get('logger.factory')->willReturn($this->mockLogger->reveal());
    $mockContainer->get('messenger')->willReturn($this->mockMessenger->reveal());
    $mockContainer->get('current_user')->willReturn($this->mockCurrentUser->reveal());
    $mockContainer->get('file_system')->willReturn($this->mockFileSystem->reveal());
    $mockContainer->get('file.mime_type.guesser')->willReturn($this->mimeTypeGuesser->reveal());

    $mockStringTranslation = $this->prophesize(TranslationInterface::class);
    $mockStringTranslation->translate(Argument::any())->willReturnArgument(0);
    $mockStringTranslation->translate(Argument::any(), Argument::any())->willReturnArgument(0);
    $mockStringTranslation->translateString(Argument::any())->willReturn('.');
    $mockContainer->get('string_translation')->willReturn($mockStringTranslation->reveal());

    // Email validator.
    $this->emailValidator = new EmailValidator();
    $mockContainer->get('email.validator')->willReturn($this->emailValidator);
    \Drupal::setContainer($this->mockContainer->reveal());
  }

  /**
   * Provides scenarios for getComponents().
   */
  public function getComponentsProvider() {
    return [
      [
        // Input.
        'name@example.com',
        // Expected.
        [
          'name' => '',
          'email' => 'name@example.com',
        ],
      ],
      [
        ' name@example.com',
        [
          'name' => '',
          'input' => 'name@example.com',
          'email' => 'name@example.com',
        ],
      ],
      [
        'name@example.com ',
        [
          'name' => '',
          'input' => 'name@example.com',
          'email' => 'name@example.com',
        ],
      ],
      [
        'some name <address@example.com>',
        [
          'name' => 'some name',
          'email' => 'address@example.com',
        ],
      ],
      [
        '"some name" <address@example.com>',
        [
          'name' => 'some name',
          'email' => 'address@example.com',
        ],
      ],
      [
        '<address@example.com>',
        [
          'name' => '',
          'email' => 'address@example.com',
        ],
      ],
    ];
  }

  /**
   * Test getComponents().
   *
   * @dataProvider getComponentsProvider
   */
  public function testGetComponents($input, $expected) {
    $mailSystem = new SMTPMailSystemTestHelper([], '', [], $this->mockLogger->reveal(), $this->mockMessenger->reveal(), $this->emailValidator, $this->mockConfigFactory->reveal(), $this->mockCurrentUser->reveal(), $this->mockFileSystem->reveal(), $this->mimeTypeGuesser->reveal());

    $ret = $mailSystem->publiGetComponents($input);

    if (!empty($expected['input'])) {
      $this->assertEquals($expected['input'], $ret['input']);
    }
    else {
      $this->assertEquals($input, $ret['input']);
    }

    $this->assertEquals($expected['name'], $ret['name']);
    $this->assertEquals($expected['email'], $ret['email']);
  }

  /**
   * Provides scenarios for testMailValidator().
   */
  public function mailValidatorProvider() {
    $emailValidatorPhpMailerDefault = new EmailValidatorPhpMailerDefault();
    $emailValidatorDrupal = new EmailValidator();
    return [
      'Without umlauts, PHPMailer default validator, no exception' => [
        'test@drupal.org',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorPhpMailerDefault,
        NULL,
      ],
      'With umlauts in local part, PHPMailer default validator, exception' => [
        'testm??ller@drupal.org',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorPhpMailerDefault,
        \PHPMailer\PHPMailer\Exception::class,
      ],
      'With umlauts in domain part, PHPMailer default validator, exception' => [
        'test@m??llertest.de',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorPhpMailerDefault,
        \PHPMailer\PHPMailer\Exception::class,
      ],
      'Without top-level domain in domain part, PHPMailer default validator, exception' => [
        'test@drupal',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorPhpMailerDefault,
        \PHPMailer\PHPMailer\Exception::class,
      ],
      'Without umlauts, Drupal mail validator, no exception' => [
        'test@drupal.org',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorDrupal,
        NULL,
      ],
      'With umlauts in local part, Drupal mail validator, no exception' => [
        'testm??ller@drupal.org',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorDrupal,
        NULL,
      ],
      'With umlauts in domain part, Drupal mail validator, no exception' => [
        'test@m??llertest.de',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorDrupal,
        NULL,
      ],
      'Without top-level domain in domain part, Drupal mail validator, no exception' => [
        'test@drupal',
        'PhpUnit Localhost <phpunit@localhost.com>',
        $emailValidatorDrupal,
        NULL,
      ],
    ];
  }


  /**
   * Test mail() with focus on the mail validator.
   *
   * @dataProvider mailValidatorProvider
   */
  public function testMailValidator(string $to, string $from, EmailValidatorInterface $validator, $exception) {
    $this->emailValidator = $validator;

    $mailSystem = new SMTPMailSystemTestHelper([],
      '',
      [],
      $this->mockLogger->reveal(),
      $this->mockMessenger->reveal(),
      $validator,
      $this->mockConfigFactory->reveal(),
      $this->mockCurrentUser->reveal(),
      $this->mockFileSystem->reveal(),
      $this->mimeTypeGuesser->reveal());
    $message = [
      'to' => $to,
      'from' => $from,
      'body' => 'Some test content for testMailValidatorDrupal',
      'headers' => [
        'content-type' => 'text/plain',
      ],
      'subject' => 'testMailValidatorDrupal',
    ];

    if (isset($exception)) {
      $this->expectException($exception);
    }
    // Call function
    $result = $mailSystem->mail($message);

    // More important than the result is that no exception was thrown, if
    // $exception is unset
    self::assertTrue($result);
  }

}

/**
 * Test helper for SMTPMailSystemTest.
 */
class SMTPMailSystemTestHelper extends SMTPMailSystem {

  /**
   * Exposes getComponents for testing.
   */
  public function publiGetComponents($input) {
    return $this->getComponents($input);
  }

  /**
   * Dummy of smtpMailerSend.
   */
  function smtpMailerSend($mailerArr) {
    return true;
  }

}

/**
 * Test helper email validator implementation for default behaviour of PHPMailer.php
 */
class EmailValidatorPhpMailerDefault implements EmailValidatorInterface {

  // This function validates in same way the PHPMailer class does in its default behaviour.
  public function isValid($email) {
    PHPMailer::$validator = 'php';
    return PHPMailer::validateAddress($email);
  }

}
