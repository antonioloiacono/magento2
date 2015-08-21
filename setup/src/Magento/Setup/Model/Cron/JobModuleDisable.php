<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Model\Cron;

use Magento\Framework\Module\PackageInfoFactory;
use Magento\Setup\Console\Command\AbstractSetupCommand;
use Magento\Setup\Model\ObjectManagerProvider;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Job that handles module disable
 */
class JobModuleDisable extends AbstractJob
{
    /**
     * @var Magento\Framework\Module\PackageInfoFactory
     */
    private $packageInfoFactory;

    /**
     * Constructor
     *
     * @param AbstractSetupCommand $command
     * @param ObjectManagerProvider $objectManagerProvider
     * @param OutputInterface $output
     * @param PackageInfoFactory $packageInfoFactory
     * @param Status $status
     * @param string $name
     * @param array $params
     */
    public function __construct(
        AbstractSetupCommand $command,
        ObjectManagerProvider $objectManagerProvider,
        OutputInterface $output,
        PackageInfoFactory $packageInfoFactory,
        Status $status,
        $name,
        $params = []
    ) {
        $this->command = $command;
        $this->output = $output;
        $this->status = $status;
        $this->packageInfoFactory = $packageInfoFactory;
        parent::__construct($output, $status, $objectManagerProvider, $name, $params);
    }

    /**
     * Execute job
     *
     * @throws \RuntimeException
     * @return void
     */
    public function execute()
    {
        try {
            $this->params['command'] = 'module:disable';
            $this->params['--clear-static-content'] = true;

            //convert composer package names to internal magento module name
            $packageInfo = $this->packageInfoFactory->create();
            foreach ($this->params['components'] as $compObj) {
                if(isset($compObj['name']) && (!empty($compObj['name']))) {
                    $moduleNames[] = $packageInfo->getModuleName($compObj['name']);
                }
                else {
                    throw new \RuntimeException('component name is not set.');
                }
            }

            //prepare the arguments to invoke Symfony run()
            $arguments['command'] = 'module:disable';
            $arguments['--clear-static-content'] = true;
            $arguments['module'] = $moduleNames;

            $this->command->run(new ArrayInput($arguments), $this->output);

            //perform the generated file cleanup
            $this->performCleanup();

        } catch (\Exception $e) {
            $this->status->toggleUpdateError(true);
            throw new \RuntimeException(sprintf('Could not complete %s successfully: %s', $this, $e->getMessage()));
        }
    }
}
