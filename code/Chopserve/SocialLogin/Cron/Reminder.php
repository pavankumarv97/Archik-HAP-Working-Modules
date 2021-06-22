<?php
 
    namespace Chopserve\Sociallogin\Cron;
    use \Psr\Log\LoggerInterface;
    use Chopserve\SocialLogin\Model\SocialLoginRepository;
 
    class Reminder
    {
        protected $logger;
        protected $socialLoginRepository;
        public function __construct(LoggerInterface $logger, SocialLoginRepository $socialLoginRepository)
        {
            $this->logger = $logger;
            $this->socialLoginRepository = $socialLoginRepository;
        }

        public function execute()
        {
            $this->socialLoginRepository->sendMailToUsers('amalpaul.c@hap.in','order_id','storeownermail');
            $this->socialLoginRepository->sendMailToUsers('mohanade2503@gmail.com','order_id','storeownermail');
            $this->logger->debug('new msg to print');
            $this->logger->info('new msg to print');
            return $this;
        }
    }