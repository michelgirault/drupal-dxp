<?php

namespace Drupal\unomi\UnomiConnector;

use Drupal\Core\Form\FormStateInterface;

/**
 * Basic auth functionality for a Unomi connector.
 */
trait BasicAuthTrait {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'username' => '',
      'password' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['auth'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('HTTP Basic Authentication'),
      '#description' => $this->t('If your Unomi server is protected by basic HTTP authentication, enter the login data here.'),
      '#collapsible' => TRUE,
      '#collapsed' => empty($this->configuration['username']),
    ];

    $form['auth']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => isset($this->configuration['username']) ? $this->configuration['username'] : '',
      '#required' => TRUE,
    ];

    $form['auth']['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#description' => $this->t('If this field is left blank and the HTTP username is filled out, the current password will not be changed.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Since the form is nested into another, we can't simply use #parents for
    // doing this array restructuring magic. (At least not without creating an
    // unnecessary dependency on internal implementation.)
    $values += $values['auth'];

    // For password fields, there is no default value, they're empty by default.
    // Therefore we ignore empty submissions if the user didn't change either.
    if ($values['password'] === ''
      && isset($this->configuration['username'])
      && $values['username'] === $this->configuration['username']) {
      $values['password'] = $this->configuration['password'];
      \Drupal::messenger()->addStatus('Password was unchanged');
    }

    foreach ($values['auth'] as $key => $value) {
      $form_state->setValue($key, $value);
    }

    // Clean-up the form to avoid redundant entries in the stored configuration.
    $form_state->unsetValue('auth');

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function viewSettings() {
    $vars = [
      '%user' => $this->configuration['username'],
      '%pass' => str_repeat('*', strlen($this->configuration['password'])),
    ];

    $info[] = [
      'label' => $this->t('Basic HTTP authentication'),
      'info' => $this->t('Username: %user | Password: %pass', $vars),
    ];

    return $info;
  }

}
