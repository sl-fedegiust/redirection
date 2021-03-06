<?php


namespace Dnetix\Redirection\Message;

use Dnetix\Redirection\Contracts\Entity;
use Dnetix\Redirection\Traits\FieldsTrait;
use Dnetix\Redirection\Traits\LoaderTrait;
use Dnetix\Redirection\Entities\Payment;
use Dnetix\Redirection\Entities\Person;
use Dnetix\Redirection\Entities\Subscription;

class RedirectRequest extends Entity
{
    use LoaderTrait, FieldsTrait;

    protected $locale = 'es_CO';
    /**
     * @var Person
     */
    protected $payer;
    /**
     * @var Person
     */
    protected $buyer;
    /**
     * @var Payment
     */
    protected $payment;
    /**
     * @var Subscription
     */
    protected $subscription;
    protected $returnUrl;
    protected $paymentMethod;
    protected $cancelUrl;
    protected $ipAddress;
    protected $userAgent;
    protected $expiration;

    protected $captureAddress;
    protected $skipResult = false;

    public function __construct($data = [])
    {
        // Setting the default values
        if (!isset($data['expiration']))
            $this->expiration = date('c', strtotime('+1 day'));
        if (!isset($data['userAgent']))
            $this->userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        if (!isset($data['ipAddress']))
            $this->ipAddress = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];

        $this->load($data, ['locale', 'returnUrl', 'paymentMethod', 'cancelUrl', 'ipAddress', 'userAgent', 'expiration', 'captureAddress', 'skipResult']);

        if (isset($data['payer']))
            $this->setPayer($data['payer']);

        if (isset($data['buyer']))
            $this->setBuyer($data['buyer']);

        if (isset($data['payment']))
            $this->setPayment($data['payment']);

        if (isset($data['subscription']))
            $this->setSubscription($data['subscription']);

        if (isset($data['fields']))
            $this->setFields($data['fields']);
    }

    public function locale()
    {
        return $this->locale;
    }

    public function language()
    {
        return strtoupper(substr($this->locale(), 0, 2));
    }

    public function payer()
    {
        return $this->payer;
    }

    public function buyer()
    {
        return $this->buyer;
    }

    /**
     * @return Payment
     */
    public function payment()
    {
        return $this->payment;
    }

    /**
     * @return Subscription
     */
    public function subscription()
    {
        return $this->subscription;
    }

    public function cancelUrl()
    {
        return $this->cancelUrl;
    }

    public function returnUrl()
    {
        return $this->returnUrl;
    }

    public function ipAddress()
    {
        return $this->ipAddress;
    }

    public function userAgent()
    {
        return $this->userAgent;
    }

    /**
     * A redirect request itself doesnt have a reference, but it should
     * know how to get it
     * @return mixed
     */
    public function reference()
    {
        if ($this->payment())
            return $this->payment()->reference();

        return $this->subscription()->reference();
    }

    public function setPayer($person)
    {
        if (is_array($person)) {
            $person = new Person($person);
        }
        $this->payer = $person;
        return $this;
    }

    public function setBuyer($person)
    {
        if (is_array($person)) {
            $person = new Person($person);
        }
        $this->buyer = $person;
        return $this;
    }

    public function setPayment($payment)
    {
        if (is_array($payment)) {
            $payment = new Payment($payment);
        }
        $this->payment = $payment;
        return $this;
    }

    public function setSubscription($subscription)
    {
        if (is_array($subscription)) {
            $subscription = new Subscription($subscription);
        }
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * Returns the expiration datetime for this request
     * @return string
     */
    public function expiration()
    {
        return $this->expiration;
    }

    public function paymentMethod()
    {
        return $this->paymentMethod;
    }

    public function captureAddress()
    {
        return !!$this->captureAddress;
    }

    public function skipResult()
    {
        return filter_var($this->skipResult, FILTER_VALIDATE_BOOLEAN);
    }

    public function toArray()
    {
        return $this->arrayFilter([
            'locale' => $this->locale(),
            'payer' => $this->payer() ? $this->payer()->toArray() : null,
            'buyer' => $this->buyer() ? $this->buyer()->toArray() : null,
            'payment' => $this->payment() ? $this->payment()->toArray() : null,
            'subscription' => $this->subscription() ? $this->subscription()->toArray() : null,
            'fields' => $this->fieldsToArray(),
            'returnUrl' => $this->returnUrl(),
            'paymentMethod' => $this->paymentMethod(),
            'cancelUrl' => $this->cancelUrl(),
            'ipAddress' => $this->ipAddress(),
            'userAgent' => $this->userAgent(),
            'expiration' => $this->expiration(),
            'captureAddress' => $this->captureAddress(),
            'skipResult' => $this->skipResult(),
        ]);
    }

}