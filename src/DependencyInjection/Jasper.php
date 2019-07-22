<?php
/**
 * Created by PhpStorm.
 * User: smilexxxx
 * Date: 20/07/2019
 * Time: 14:11
 */

namespace App\DependencyInjection;


use PHPJasper\PHPJasper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Jasper
{
    /**
     * @var PHPJasper
     */
    private $jasper;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var string
     */
    private $uploadDir;

    public function __construct(Filesystem $filesystem, LoggerInterface $logger, string $reportDir, string $uploadDir)
    {
        $this->jasper = new PHPJasper();
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->reportDir = $reportDir;
        $this->uploadDir = $uploadDir;
    }

    /**
     * Save report and compile
     *
     * @param $report
     * @throws \PHPJasper\Exception\ErrorCommandExecutable
     * @throws \PHPJasper\Exception\InvalidCommandExecutable
     * @throws \PHPJasper\Exception\InvalidInputFile
     * @throws \PHPJasper\Exception\InvalidResourceDirectory
     */
    public function create($report)
    {
        $file = $this->saveReport($report);

        $nameReport = $file->getBasename(".".$file->getExtension());

        $tmpDir = '/tmp/'.hash('sha256', $nameReport);

        $this->jasper->compile(
            $file->getPathname(),
            $tmpDir
        )->execute();

        $reportName = $this->reportDir.$nameReport.'.jasper';

        $this->filesystem->copy("$tmpDir.jasper", $reportName);
        $this->logger->info("Compile report [$reportName]");
    }

    /**
     * @param $nameReport
     * @throws \PHPJasper\Exception\InvalidFormat
     * @throws \PHPJasper\Exception\InvalidInputFile
     */
    public function process($nameReport)
    {
        $reportName = $this->reportDir.$nameReport.'.jasper';
        //$tmpDir = '/tmp/'.hash('sha256', $nameReport.(new \DateTime())->getTimestamp());
        //$this->filesystem->mkdir($tmpDir);

        $output = __DIR__ . '/vendor/geekcom/phpjasper/examples';
        $options = [
            'format' => ['pdf', 'xls'],
        ];
        dump($this->jasper->process($reportName, $output, $options)->output());
    }

    /**
     * Save report for compile
     *
     * @param UploadedFile $file
     * @return File
     */
    private function saveReport(UploadedFile $file)
    {
        try {
            return $file->move($this->uploadDir, $file->getClientOriginalName());
        } catch (FileException $e) {

            $this->logger->error('failed to upload report: '.$e->getMessage());
            throw new FileException('Failed to upload file');
        }
    }
}