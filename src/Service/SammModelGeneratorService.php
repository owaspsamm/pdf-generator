<?php

declare(strict_types=1);

namespace App\Service;

use App\Adapter\CodificPdf;
use App\Entity\Activity;
use App\Entity\Answer;
use App\Entity\BusinessFunction;
use App\Entity\Practice;
use App\Entity\PracticeLevel;
use App\Entity\Question;
use App\Entity\Stream;
use App\Repository\BusinessFunctionRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SammModelGeneratorService
{
    private CodificPdf $pdf;

    private const COLOR_MAROON = [128, 0, 0];
    private const COLOR_WHISPER = [249, 246, 246];
    private const COLOR_GLACIER = [113, 184, 184];
    private const COLOR_CHARCOAL = [70, 70, 70];
    private const COLOR_BLACK = [0, 0, 0];
    private const COLOR_BLACK_CORAL = [67, 91, 112];
    private const COLOR_TENNE_TAWNY = [202, 89, 2];
    private const COLOR_FERN_GREEN = [69, 113, 45];
    private const COLOR_EGGPLANT = [117, 72, 88];
    private const COLOR_DAVYS_GREY = [91, 91, 98];
    private const COLOR_VERY_LIGHT_GREY = [200, 200, 200];


    /**
     * @throws \Safe\Exceptions\MiscException
     */
    public function __construct(private ParameterBagInterface $parameterBag, private BusinessFunctionRepository $businessFunctionRepository)
    {
    }

    private function getMulticellHeight(
        $w,
        $h,
        $txt,
        $border = 1,
        $align = 'L',
        $fill = false,
        $ln = 1,
        $x = null,
        $y = null,
        $reseth = true,
        $stretch = 0,
        $ishtml = false,
        $autopadding = true,
        $maxh = 0
    ) {
        // store current object
        $this->pdf->startTransaction();
        // store starting values
        $start_y = $this->pdf->GetY();
        $start_page = $this->pdf->getPage();
        // call your printing functions with your parameters
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $this->pdf->MultiCell(
            $w = 0,
            $h = 0,
            $txt,
            $border = 1,
            $align = 'L',
            $fill = false,
            $ln = 1,
            $x = null,
            $y = null,
            $reseth = true,
            $stretch = 0,
            $ishtml = false,
            $autopadding = true,
            $maxh = 0
        );
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // get the new Y
        $end_y = $this->pdf->GetY();
        $end_page = $this->pdf->getPage();
        // calculate height
        $height = 0;
        if ($end_page == $start_page) {
            $height = $end_y - $start_y;
        } else {
            for ($page = $start_page; $page <= $end_page; ++$page) {
                $this->pdf->setPage($page);
                if ($page == $start_page) {
                    // first page
                    $height += $this->pdf->getPageHeight($page) - $start_y - $this->pdf->getBreakMargin($page);
                } elseif ($page == $end_page) {
                    // last page
                    $height += $end_y - $this->pdf->getMargins()['top'];
                } else {
                    $height += $this->pdf->getPageHeight($page) - $this->pdf->getMargins()['top'] - $this->pdf->getBreakMargin($page);
                }
            }
        }
        // restore previous object
        $this->pdf->rollbackTransaction(true);

        return $height;
    }


    private function setTextColorByRGBArray($array)
    {
        $this->pdf->setTextColor($array[0], $array[1], $array[2]);
    }

    public function generate()
    {
        // load fonts for tcpdf from folder which is under git control.
        !defined('K_PATH_FONTS') && define('K_PATH_FONTS', $this->parameterBag->get('kernel.project_dir').'/tcpdf/tcpdf_fonts/');


        // Start tcpdf stuff
        $this->pdf = new CodificPdf(PDF_PAGE_ORIENTATION, "pt", 'A4', true, 'UTF-8', false);
        $this->pdf->setMargins(60, 0, 60, true);
        $this->pdf->setAutoPageBreak(true, 60);

        $this->addPresentingPage();
        $this->printProperties();

        $this->pdf->setFooterFunction(function (CodificPdf $codificPdf) {
            $this->footer($codificPdf);
        });
        $this->pdf->setHeaderFunction(function (CodificPdf $codificPdf) {
            $this->TOCHeader($codificPdf);
        });
        $this->pdf->setPrintHeader(false);

        $this->pdf->Bookmark('About OWASP SAMM', 0, 0, $this->pdf->getPage() + 1, '', self::COLOR_MAROON);
        $this->addAboutOwaspSammPage1();
        $this->addAboutOwaspSammPage2();
        $this->addAboutOwaspSammPage3();
        $this->addAboutOwaspSammPage4();
        $this->addAboutOwaspSammPage5();
        $this->addBusinessFunctionPages();
        $this->pdf->Bookmark('Credits', 0, 0, $this->pdf->getPage() + 1, '', self::COLOR_MAROON);
        $this->addCreditsPage();
        $this->pdf->Bookmark('Sponsors', 0, 0, $this->pdf->getPage() + 1, '', self::COLOR_MAROON);
        $this->addSponsorsPage();
        $this->pdf->Bookmark('License', 0, 0, $this->pdf->getPage() + 1, '', self::COLOR_MAROON);
        $this->addLicensePage();

        $this->addTOCPage();

        return $this->pdf->Output("report.pdf", "S");
    }

    private function addTOCPage()
    {

        $this->pdf->setPrintHeader(true);
        $margins = $this->pdf->getMargins();
        $this->pdf->setMargins($margins['left'], 110, $margins['right']);

        $this->pdf->addTOCPage('P');
        $this->pdf->setMargins($margins['left'], 125, $margins['right']);
        $this->pdf->setAutoPageBreak(true, 60);

        $base = $this->parameterBag->get('kernel.project_dir').'/SAMM-PDF/html/';
        $bookmarkTemplates[0] = file_get_contents($base.'table0.html');
        $bookmarkTemplates[1] = file_get_contents($base.'table1.html');
        $bookmarkTemplates[2] = file_get_contents($base.'table2.html');
        $this->pdf->addHTMLTOC(2, 'TOC', $bookmarkTemplates, true, 'B', self::COLOR_MAROON);
        $this->pdf->endTOCPage();

        $this->pdf->setPrintHeader(false);
        $this->pdf->setAutoPageBreak(true, 60);
    }

    private function addPresentingPage()
    {
        $this->pdf->AddPage('P');

        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $bMargin = $this->pdf->getBreakMargin();
        $auto_page_break = $this->pdf->getAutoPageBreak();
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->pdf->Rect(0, 0, $this->pdf->getPageWidth(), $this->pdf->getPageHeight(), 'DF', [], self::COLOR_GLACIER);

        $this->pdf->Image(
            $this->parameterBag->get('kernel.project_dir').'/SAMM-PDF/images/SAMM-Logo.png',
            88,
            341,
            405,
            149,
            '',
            '',
            '',
            false,
            300,
            '',
            false,
            false,
            0
        );

        $this->pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        $this->pdf->setPageMark();

        $this->pdf->setPrintHeader(true);
        $this->pdf->setPrintFooter(true);
    }

    private function setDefaultFont()
    {
        $this->setFontOpensans(13, self::COLOR_CHARCOAL, 1.78);
        $this->pdf->setFontSpacing(-0.035);
    }

    private function setFontProperties($size, $color, $ratio)
    {
        $this->setTextColorByRGBArray($color);
        $this->pdf->setFontSize($size);
        $this->pdf->setCellHeightRatio($ratio - ($size / 1000));
        $this->pdf->setFontSpacing(-0.02);
    }

    private function setFontDaysone($size, $color = self::COLOR_CHARCOAL, $ratio = 1.3)
    {
        $this->pdf->SetFont("daysone");
        $this->setFontProperties($size, $color, $ratio);
    }

    private function setFontOpensans($size, $color = self::COLOR_CHARCOAL, $ratio = 1.4)
    {
        $this->pdf->SetFont("opensans");
        $this->setFontProperties($size, $color, $ratio);
    }

    private function setFontOpensansSemi($size, $color = self::COLOR_CHARCOAL, $ratio = 1.4)
    {
        $this->pdf->SetFont("opensanssemib", );
        $this->setFontProperties($size, $color, $ratio);
    }

    protected function footer(CodificPdf $codificPdf)
    {
        $codificPdf->setFooterMargin(24);

        if ($codificPdf->PageNo() != 1 && $codificPdf->getPageOrientation() != 'L') {
            $l = $codificPdf->getL();
            $wPage = isset($l['w_page']) ? $l['w_page'].' ' : '';
            if (empty($codificPdf->getPageGroups())) {
                $pageNumTxt = $wPage.$codificPdf->getAliasNumPage();
            } else {
                $pageNumTxt = $wPage.$codificPdf->getPageNumGroupAlias();
            }
            $this->setTextColorByRGBArray(self::COLOR_CHARCOAL);
            $codificPdf->SetFont('Open Sans', '', 10);
            $codificPdf->setRightMargin(0);
            $codificPdf->setLeftMargin(0);
            $this->addTextField("OWASP SAMM V2.0", [14, 814]);
            $this->addTextField($pageNumTxt, [14, 814], 'R');
        }
    }

    protected function TOCHeader(CodificPdf $codificPdf)
    {
        $codificPdf->setFooterMargin(24);
        $this->addPageHeader('Table Of Contents', self::COLOR_WHISPER, self::COLOR_GLACIER);
    }

    /**
     * Print some properties for this pdf
     * @param string $title the title
     * @param bool $autoBreak whether to have autobreak
     * @return void
     */
    protected function printProperties(string $title = "", bool $autoBreak = true)
    {
        $bMargin = $this->pdf->getBreakMargin();

        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('CODIFIC PDF Generator');
        $this->pdf->SetTitle($title);
        $this->pdf->SetSubject($title);
        $this->pdf->SetMargins(60, 25, 60);
        $this->pdf->SetPrintHeader(true);
        if ($autoBreak) {
            $this->pdf->SetAutoPageBreak(true, $bMargin);
        } else {
            $this->pdf->SetAutoPageBreak(false);
        }
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    private function addPageHeader($text, $textColor, $mainColor)
    {
        $this->pdf->Rect(0, 0, 595, 97, 'DF', ['all' => ['width' => 0, 'color' => $mainColor]], $mainColor);

        $this->setFontDaysone(27, $textColor);


        $this->pdf->MultiCell(595, 97, $text, 0, 'C', false, 1, 0, 0, true, 0, false, true, 97, 'M');


        $this->setDefaultFont();
    }

    private function addPageTitle(string $text, array $coordinates, $textColor)
    {
        $this->setFontDaysone(21, $textColor);
        $this->addTextField($text, $coordinates);

        $this->setDefaultFont();
    }

    private function addTextField(
        string $text,
        array $coordinates,
        $align = 'L',
        $width = 0,
        $height = 0,
        $html = false,
        array $padding = [],
        $valign = 'T'
    ): void {
        $this->pdf->setCellPaddings(...$padding);

        if ($html) {
            $pad = -33;
            $this->pdf->writeHTMLCell($width, $height, $coordinates[0] + $pad, $coordinates[1], $text, 0, 1, false, true, $align);
        } else {
            $this->pdf->MultiCell(
                $width,
                $height,
                $text,
                0,
                $align,
                false,
                1,
                $coordinates[0],
                $coordinates[1],
                true,
                0,
                false,
                true,
                $height,
                $valign
            );
        }

        $this->pdf->setCellPadding(0);
    }

    private function addAboutOwaspSammTemplate($title)
    {
        $this->pdf->AddPage('P');


        $this->addPageHeader('About OWASP SAMM', self::COLOR_WHISPER, self::COLOR_GLACIER);

        if ($title) {
            $this->addPageTitle($title, [60, 120], self::COLOR_GLACIER);
            $this->pdf->Bookmark($title, 1, 0, '', '', self::COLOR_MAROON);
        }
    }

    private function addAboutOwaspSammPage1()
    {
        $this->addAboutOwaspSammTemplate('What is OWASP SAMM?');

        $txt = "SAMM stands for Software Assurance Maturity Model.\n\nOur mission is to provide an effective and measurable way for all types of organizations to analyze and improve their software security posture. We want to raise awareness and educate organizations on how to design, develop, and deploy secure software through our self-assessment model. SAMM supports the complete software lifecycle and is technology and process agnostic. We built SAMM to be evolutive and risk-driven in nature, as there is no single recipe that works for all organizations.";
        $this->addTextField($txt, [60, 170]);

        $imageBasePath = $this->parameterBag->get('kernel.project_dir').'/SAMM-PDF/images/';
        $top = $this->pdf->GetY() + 38;
        $this->pdf->Image($imageBasePath.'about-dots.png', 60, $top, 37, 39);
        $txt = "MEASURABLE\nDefined maturity levels across security practices";
        $this->addTextField($txt, [130, $top - 17], 'L', 0, 39 + 34, false, [], "M");
        $top += 39 + 34;

        $this->pdf->Image($imageBasePath.'about-cross.png', 56, $top, 43, 42);
        $txt = "ACTIONABLE\nClear pathways for improving maturity levels";
        $this->addTextField($txt, [130, $top - 17], 'L', 0, 42 + 34, false, [], "M");
        $top += 42 + 34;

        $this->pdf->Image($imageBasePath.'about-human.png', 56, $top, 39, 40);
        $txt = "VERSATILE\nTechnology, process, and organization agnostic";
        $this->addTextField($txt, [130, $top - 17], 'L', 0, 40 + 34, false, [], "M");
        $top += 40 + 34;

        $txt = 'The OWASP SAMM community is powered by security knowledgeable volunteers from businesses and educational organizations. The global community works to create freely-available articles, methodologies, documentation, tools, and technologies.';
        $this->addTextField($txt, [60, $top]);
    }

    private function addAboutOwaspSammPage2()
    {
        $this->addAboutOwaspSammTemplate('The OWASP SAMM Model');

        $txt = "SAMM is a prescriptive model, an open framework which is simple to use, fully defined, and measurable. The solution details are easy enough to follow even for non-security personnel. It helps organizations analyze their current software security practices, build a security program in defined iterations, show progressive improvements in secure practices, and define and measure security-related activities.\n\nSAMM was defined with flexibility in mind so that small, medium, and large organizations using any style of development can customize and adopt it. It provides a means of knowing where your organization is on its journey towards software assurance and understanding what is recommended to move to the next level of maturity.\n\nSAMM does not insist that all organizations achieve the maximum maturity level in every category. Each organization can determine the target maturity level for each Security Practice that is the best fit and adapt the available templates for their specific needs.";
        $this->addTextField($txt, [60, 170]);
    }


    private function addOutlineRectangle($coordinates, $size, $border, $borderWidth = 0, $color = [255, 255, 255], $style = 'DF')
    {
        $borderWidth /= 2;
        $this->pdf->Rect(
            $coordinates[0] + $borderWidth,
            $coordinates[1] + $borderWidth,
            $size[0] - 2 * $borderWidth,
            $size[1] - 2 * $borderWidth,
            $style,
            $border,
            $color
        );
    }

    private function addLine($start, $length, $style, $borderWidth = 0, $vertical = false)
    {
        $borderWidth /= 2;
        $length = ($vertical) ? -1 * $length : $length;
        $end = $start;
        $end[(int)$vertical] += $length;
        $borderWidthAdjust = ($vertical) ? [$borderWidth, 0] : [0, $borderWidth];
        $this->pdf->Line(
            $start[0] - $borderWidthAdjust[0],
            $start[1] - $borderWidthAdjust[1],
            $end[0] - $borderWidthAdjust[0],
            $end[1] - $borderWidthAdjust[1],
            $style
        );
    }

    private function addAboutOwaspSammPage3()
    {
        $this->addAboutOwaspSammTemplate('OWASP SAMM Structure');

        $txt = "SAMM is based around 15 security practices grouped into 5 business functions. Every security practice contains a set of activities, structured into 3 maturity levels. The activities on a lower maturity level are typically easier to execute and require less formalization than the ones on a higher maturity level.";
        $this->addTextField($txt, [60, 170]);

        $txt = "At the highest level, SAMM defines five business functions. Each business function is a category of activities that any organization involved with software development must fulfill to some degree.\n\nEach business function has three security practices, areas of security-related activities that build assurance for the related business function.";
        $this->addTextField($txt, [60, 571]);

        $borderWidth = 2;
        $style = ['width' => $borderWidth, 'color' => self::COLOR_GLACIER];
        $border = ['all' => $style];

        $this->addOutlineRectangle([84, 484], [101, 64], $border, $borderWidth);
        $this->addOutlineRectangle([209, 389], [79, 54], $border, $borderWidth);
        $this->addOutlineRectangle([312, 351], [73, 32], $border, $borderWidth);
        $this->addOutlineRectangle([312, 452], [73, 32], $border, $borderWidth);
        $this->addOutlineRectangle([407, 298], [104, 40], $border, $borderWidth);
        $this->addOutlineRectangle([407, 347], [104, 40], $border, $borderWidth);
        $this->addOutlineRectangle([407, 396], [104, 40], $border, $borderWidth);

        $this->addLine([185, 516], 14, $style, $borderWidth);
        $this->addLine([199, 516], 100, $style, $borderWidth, true);
        $this->addLine([197, 418], 14, $style, $borderWidth);
        $this->addLine([288, 418], 14, $style, $borderWidth);
        $this->addLine([302, 468], 100, $style, $borderWidth, true);
        $this->addLine([300, 468], 14, $style, $borderWidth);
        $this->addLine([300, 370], 14, $style, $borderWidth);
        $this->addLine([383, 369], 14, $style, $borderWidth);
        $this->addLine([397, 416], 100, $style, $borderWidth, true);
        $this->addLine([395, 416], 14, $style, $borderWidth);
        $this->addLine([395, 369], 14, $style, $borderWidth);
        $this->addLine([395, 318], 14, $style, $borderWidth);


        $this->setFontOpensansSemi(17);

        $text = "Business\nFunction";
        $this->addTextField($text, [84, 491], "C", 101);

        $this->setFontOpensansSemi(13);
        $text = "Security\nPractice";
        $this->addTextField($text, [209, 398], "C", 79);

        $this->setFontOpensansSemi(10);
        $text = "Stream A";
        $this->addTextField($text, [312, 360], "C", 73);
        $text = "Stream B";
        $this->addTextField($text, [312, 461], "C", 73);

        $text = "Activity\nMaturity level 1";
        $this->addTextField($text, [407, 304], "C", 104);
        $text = "Activity\nMaturity level 2";
        $this->addTextField($text, [407, 353], "C", 104);
        $text = "Activity\nMaturity level 3";
        $this->addTextField($text, [407, 402], "C", 104);

        $this->setDefaultFont();
    }

    private function addAboutOwaspSammPage4()
    {
        $this->addAboutOwaspSammTemplate(null);

        $this->pdf->setTextColor(70, 70, 70);
        $this->pdf->SetFont("opensans", '', 13);

        $txt = "Security practices have activities, grouped in logical flows and divided into two streams. Streams cover different aspects of a practice and have their own objectives, aligning and linking the activities in the practice over the different maturity levels.";
        $txt .= "\n\nFor each security practice, SAMM defines three maturity levels. Each level has a successively more sophisticated objective with specific activities, and more strict success metrics.";
        $txt .= "\n\nThe structure and setup of the SAMM model support";
        $txt .= "\n\n• the assessment of the organization’s current software security posture";
        $txt .= "\n• the definition of the organization’s target";
        $txt .= "\n• the definition of an implementation roadmap to get there";
        $txt .= "\n• prescriptive advice on how to implement particular activities";

        $this->pdf->MultiCell(0, 0, $txt, [], 'L', false, 0, 60, 120, true, 0, false, true, 0, 'M');
    }

    private function addBusinessFunctionTableForPage5(BusinessFunction $businessFunction, $color, array $offsets): void
    {
        $verticalPad = 16;
        $this->addOutlineRectangle($offsets, [136, 43], [], 0, $color, "F");
        $this->setFontOpensansSemi(13, self::COLOR_WHISPER);
        $this->addTextField($businessFunction->getName(), $offsets, 'C', 136, 43, false, [], 'M');

        $stumpOffsets = [];
        $borderWidth = 2;
        $border = ['all' => ['width' => $borderWidth, 'color' => $color]];

        foreach ($this->getOrderedBusinessFunctionPractices($businessFunction) as $practice) {
            $offsetY = $this->pdf->GetY() + $verticalPad;
            $width = 136;
            $height = 50;
            $this->addOutlineRectangle([$offsets[0], $offsetY], [$width, $height], $border, $borderWidth, self::COLOR_WHISPER);
            $this->setFontOpensansSemi(11, $color);
            $this->addTextField($this->getPracticeName($practice), [$offsets[0], $offsetY], 'C', $width, $height, false, [10, 0, 10, 0], 'M');

            $offsetY = $this->pdf->GetY() - $borderWidth;
            $practiceSize = [69, 40];

            foreach ($this->getOrderedPracticeStreams($practice) as $index => $stream) {
                $practiceOffsets = [$offsets[0] + ($index * ($practiceSize[0] - $borderWidth)), $offsetY];
                $this->addOutlineRectangle($practiceOffsets, $practiceSize, $border, $borderWidth, self::COLOR_WHISPER);
                $this->setFontOpensans(8, $color);
                $this->addTextField(
                    $this->getStreamName($stream),
                    $practiceOffsets,
                    'C',
                    $practiceSize[0],
                    $practiceSize[1],
                    false,
                    [6, 0, 6, 0],
                    'M'
                );
            }
            $stumpSize = [19, $verticalPad + $borderWidth];
            $stumpsDistance = 18;

            $stumpOffsets = [
                $offsets[0] + $practiceSize[0] - $borderWidth - (+$stumpsDistance / 2 + $stumpSize[0]),
                $this->pdf->GetY() - $borderWidth / 2,
            ];
            $this->addOutlineRectangle($stumpOffsets, $stumpSize, $border, $borderWidth, $color);

            $stumpOffsets[0] = $offsets[0] + $practiceSize[0] + $stumpsDistance / 2;
            $this->addOutlineRectangle($stumpOffsets, $stumpSize, $border, $borderWidth, $color);
        }

        $endPieceSize = [58, 21];
        $this->setFontOpensans(10, self::COLOR_WHISPER, 1.4);
        $endPieceOffsets = [$offsets[0], $stumpOffsets[1] + $verticalPad];
        $this->addOutlineRectangle($endPieceOffsets, $endPieceSize, $border, $borderWidth, $color);
        $this->addTextField("Stream A", $endPieceOffsets, 'C', $endPieceSize[0], $endPieceSize[1], false, [7, 0, 7, 0], 'M');


        $endPieceOffsets[0] = $stumpOffsets[0];
        $this->addOutlineRectangle($endPieceOffsets, $endPieceSize, $border, $borderWidth, $color);
        $this->addTextField("Stream B", $endPieceOffsets, 'C', $endPieceSize[0], $endPieceSize[1], false, [7, 0, 7, 0], 'M');
    }

    private function addAboutOwaspSammPage5(): void
    {
        $this->pdf->AddPage('L');
        $this->pdf->Bookmark('OWASP SAMM Structure Diagram', 1, 0, '', '', self::COLOR_MAROON);

        $this->pdf->SetFont("daysone", '', 27);
        $this->addOutlineRectangle([0, 0], [842, 97], [], 0, self::COLOR_GLACIER);
        $this->setTextColorByRGBArray(self::COLOR_WHISPER);
        $this->pdf->MultiCell(842, 97, 'About OWASP SAMM', 0, 'C', false, 1, 0, 0, true, 0, false, true, 95, 'M');

        $this->setDefaultFont();


        foreach ($this->businessFunctionRepository->findOptimized() as $index => $businessFunction) {
            $color = $this->getBusinessFunctionColor($businessFunction);
            $this->addBusinessFunctionTableForPage5($businessFunction, $color, [49 + $index * (16 + 136), 138]);
        }
    }

    private function getBusinessFunctionColor(BusinessFunction $businessFunction): array
    {
        $color = self::COLOR_BLACK;
        if ($this->businessFunctionIsGovernance($businessFunction)) {
            $color = self::COLOR_BLACK_CORAL;
        } elseif ($this->businessFunctionIsDesign($businessFunction)) {
            $color = self::COLOR_TENNE_TAWNY;
        } elseif ($this->businessFunctionIsImplementation($businessFunction)) {
            $color = self::COLOR_FERN_GREEN;
        } elseif ($this->businessFunctionIsOperations($businessFunction)) {
            $color = self::COLOR_DAVYS_GREY;
        } elseif ($this->businessFunctionIsVerification($businessFunction)) {
            $color = self::COLOR_EGGPLANT;
        }

        return $color;
    }

    private function businessFunctionIsGovernance(BusinessFunction $businessFunction): bool
    {
        return $businessFunction->getExternalId() === "102ad02df5dc4a8eb3837ef4ca2c1af4";
    }

    private function businessFunctionIsDesign(BusinessFunction $businessFunction): bool
    {
        return $businessFunction->getExternalId() === "88c296acaae841a2b2fc5314bff44cb4";
    }

    private function businessFunctionIsImplementation(BusinessFunction $businessFunction): bool
    {
        return $businessFunction->getExternalId() === "8aa8154b83434e73b3ca8c0e9b654417";
    }

    private function businessFunctionIsOperations(BusinessFunction $businessFunction): bool
    {
        return $businessFunction->getExternalId() === "942d679b0c9e41909f8bde728fdb1259";
    }

    private function businessFunctionIsVerification(BusinessFunction $businessFunction): bool
    {
        return $businessFunction->getExternalId() === "fa340fa1816244d79f369ae82e998368";
    }

    private function getPracticeName(Practice $practice): string
    {
        return str_replace(" and ", " & ", $practice->getName());
    }

    private function getStreamName(Stream $stream): string
    {
        $name = $stream->getName();
        if (strlen($name) > 25 && str_contains($name, '/')) {
            $name = ltrim(substr($name, strpos($name, '/') + 1));
        }

        return str_replace(" and ", " & ", $name);
    }

    private function getOrderedBusinessFunctionPractices(BusinessFunction $businessFunction)
    {

        $practices = $businessFunction->getBusinessFunctionPractices()->getValues();
        /** @var Practice[] $practices */
        usort($practices, fn(Practice $a, Practice $b) => (int)($a->getOrder() > $b->getOrder()));

        return $practices;
    }

    private function addBusinessFunctionPages()
    {

        $businessFunctions = $this->businessFunctionRepository->findOptimized();
        foreach ($businessFunctions as $businessFunction) {
            $color = $this->getBusinessFunctionColor($businessFunction);

            $practices = $this->getOrderedBusinessFunctionPractices($businessFunction);
            $this->pdf->Bookmark($businessFunction->getName(), 0, 0, $this->pdf->getPage() + 1, '', $color);
            $this->addBusinessFunctionPage1($businessFunction, $color, $practices);
            $this->addBusinessFunctionPage2($businessFunction, $color, $practices);

            $this->addPracticePages($practices, $color);
        }
    }

    private function addBusinessFunctionTemplate(BusinessFunction $businessFunction, $color)
    {
        $this->pdf->AddPage('P');
        $this->addPageHeader($businessFunction->getName(), self::COLOR_WHISPER, $color);
    }

    /**
     * @param BusinessFunction $businessFunction
     * @param $color
     * @param Practice[] $practices
     * @return void
     */
    private function addBusinessFunctionPage1(BusinessFunction $businessFunction, $color, array $practices): void
    {
        $this->addBusinessFunctionTemplate($businessFunction, $color);

        $this->addPageTitle("Business Function Overview", [60, 134], $color);
        $this->setDefaultFont();
        $this->pdf->MultiCell(475, 0, $businessFunction->getDescription(), [], "", false, 1, 60, $this->pdf->GetY() + 23, true, 0, false, true, 0);
        $this->addPageTitle("Security Practices Overview", [60, $this->pdf->GetY() + 37], $color);

        foreach ($practices as $practice) {
            $this->setFontOpensansSemi(17);
            $this->addTextField($this->getPracticeName($practice), [60, $this->pdf->GetY() + 23]);
            $this->setDefaultFont();
            $this->addTextField($practice->getShortDescription(), [60, $this->pdf->GetY() + 7]);
        }
    }

    /**
     * @param BusinessFunction $businessFunction
     * @param $color
     * @param Practice[] $practices
     * @return void
     */
    private function addBusinessFunctionPage2(BusinessFunction $businessFunction, $color, array $practices): void
    {
        $this->addBusinessFunctionTemplate($businessFunction, $color);

        $this->addPageTitle($businessFunction->getName()." Overview Table", [60, 134], $color);

        // Because some text is larger, and we set it on coords we are going to push the yAxis of the table by some number relative to the number of cells taken by the text
        $totalY = 184;

        $this->addOutlineRectangle([60, $totalY], [160, 42], ['all' => ['width' => 2, 'color' => $color]], 2);
        $this->addOutlineRectangle([217, $totalY], [160, 42], ['all' => ['width' => 2, 'color' => $color]], 2);
        $this->addOutlineRectangle([375, $totalY], [160, 42], ['all' => ['width' => 2, 'color' => $color]], 2);

        $this->pdf->SetFont("opensans", '', 10);

        $this->pdf->MultiCell(160, 42, "SECURITY PRACTICE", [], 'C', false, 1, 60, $totalY, true, 0, false, true, 42, valign: 'M');
        $this->pdf->MultiCell(160, 42, "STREAM A", [], 'C', false, 1, 217, $totalY, true, 0, false, true, 42, valign: 'M');
        $this->pdf->MultiCell(160, 42, "STREAM B", [], 'C', false, 1, 375, $totalY, true, 0, false, true, 42, valign: 'M');

        $width = 160;
        $height = 92;
        foreach ($practices as $practice) {
            $borderWidth = 2;
            $border = ['all' => ['width' => $borderWidth, 'color' => $color]];
            $practiceCoordinates = [60, $this->pdf->GetY() - $borderWidth];
            $this->addOutlineRectangle($practiceCoordinates, [$width - 1, $height], $border, $borderWidth);

            $this->setFontOpensans(17);
            $practiceNameToWrite = $this->getPracticeName($practice);
            $textHasOnlyOnceSpace = count(explode(" ", $practiceNameToWrite)) <= 2;
            if ($textHasOnlyOnceSpace) {
                $practiceNameToWrite = str_replace(" ", "\n", $practiceNameToWrite);
            }
            if (str_contains($practiceNameToWrite, "&")) {
                $practiceNameToWrite = str_replace(" & ", " &\n", $practiceNameToWrite);
            }

            $innerPadding = 7;
            $this->pdf->MultiCell(
                $width - 2 * $innerPadding,
                $height,
                $practiceNameToWrite,
                [],
                'C',
                false,
                1,
                $practiceCoordinates[0] + $innerPadding,
                $practiceCoordinates[1],
                true,
                0,
                false,
                true,
                $height,
                valign: 'M'
            );

            $this->setDefaultFont();
            foreach ($this->getOrderedPracticeStreams($practice) as $streamArrayIndex => $stream) {
                $streamCoordinates = [$practiceCoordinates[0] - 1 + ($streamArrayIndex + 1) * ($width - $borderWidth), $practiceCoordinates[1]];
                $this->addOutlineRectangle($streamCoordinates, [$width, $height], $border, $borderWidth);
                $this->pdf->MultiCell(
                    $width - 2 * $innerPadding,
                    $height,
                    $this->getStreamName($stream),
                    [],
                    'C',
                    false,
                    1,
                    $streamCoordinates[0] + $innerPadding,
                    $streamCoordinates[1],
                    true,
                    0,
                    false,
                    true,
                    $height,
                    valign: 'M'
                );
            }
        }
    }

    /**
     * @param Practice[] $practices
     * @return void
     */
    private function addPracticePages(array $practices, $color)
    {
        foreach ($practices as $practice) {
            $this->pdf->Bookmark($practice->getName(), 1, 0, $this->pdf->getPage() + 1, '', $color);
            $this->addSecurityPracticePage1($practice, $color);

            $levelActivities = $this->getPracticeActivitiesPerLevel($practice);

            $this->addSecurityPracticePage2($practice, $levelActivities, $color);
            $this->addSecurityPracticePage3($practice, $levelActivities, $color);

            $activitiesA = [];
            $activitiesB = [];
            foreach ($levelActivities as $activities) {
                $activitiesA[] = $activities[0];
                $activitiesB[] = $activities[1];
            }
            $this->addActivityPages($activitiesA, $color);
            $this->addActivityPages($activitiesB, $color);
        }
    }

    private function addBusinessFunctionName(string $text, array $coordinates, $textColor)
    {
        $this->setFontDaysone(17, $textColor);
        $this->pdf->MultiCell($coordinates[0], 0, $text, 0, 'R', false, 1, 0, $coordinates[1], true, 1, false, true, 0, 'T');

        $this->setDefaultFont();
    }

    private function addSecurityPracticeTemplate(Practice $practice, $color, $title)
    {
        $this->pdf->AddPage('P');

        $this->addPageHeader($this->getPracticeName($practice), self::COLOR_WHISPER, $color);

        $this->addBusinessFunctionName($practice->getBusinessFunction()->getName(), [581, 106], $color);

        $this->addPageTitle($title, [60, 151], $color);
        $this->pdf->Bookmark($title, 2, 0, $this->pdf->getPage(), '', $color);
    }

    private function addSecurityPracticePage1(Practice $practice, $color)
    {
        $this->addSecurityPracticeTemplate($practice, $color, "Security Practice Overview");

        $bigPad = 23;

        $practiceyDescriptionWidth = 475;
        $txt = $practice->getLongDescription();
        $this->addTextField($txt, [60, $this->pdf->GetY() + $bigPad], 'L', $practiceyDescriptionWidth);
    }


    /**
     * @param array $activities
     * @param $offsetHeight
     * @param $border
     * @param $borderWidth
     * @param $color
     * @return float
     */
    private function printMaturityLevel(array $activities, $offsetHeight, $border, $borderWidth, $color): float
    {
        $titleOffsets = [14, 12];
        $descriptionOffsets = [14, 37];
        $streamDescriptionOffsets = [[14, 14], [251, 14]];
        $activityDescriptionWidth = 210;
        $maturityDescriptionWidth = 447;

        $practiceLevel = reset($activities)->getPracticeLevel();
        $level = $practiceLevel->getMaturityLevel()->getLevel();

        $boxMargin = 14;

        $maturityLevelBoxHeight = 0;
        foreach ([false, true] as $real) {
            if ($real) {
                $this->addOutlineRectangle([60, $offsetHeight], [475, $maturityLevelBoxHeight], $border, $borderWidth);
            } else {
                $this->pdf->startTransaction();
            }

            $this->printMaturityLevelCircles([60 + 415, $offsetHeight + 14], $level, $color);

            $this->setDefaultFont();
            $text = "Maturity level $level";
            $this->addTextField($text, [60 + $titleOffsets[0], $offsetHeight + $titleOffsets[1]]);

            $this->setFontOpensans(10, self::COLOR_CHARCOAL, 1.4);
            $text = $practiceLevel->getObjective();
            $this->addTextField($text, [60 + $descriptionOffsets[0], $offsetHeight + $descriptionOffsets[1]], 'L', $maturityDescriptionWidth);

            $maturityLevelBoxHeight = $this->pdf->GetY() - $offsetHeight + $boxMargin;
            if (!$real) {
                $this->pdf->rollbackTransaction(true);
            }
        }


        $activityBoxHeight = 0;
        foreach ([false, true] as $real) {
            if ($real) {
                $this->addOutlineRectangle([60, $offsetHeight + $maturityLevelBoxHeight - $borderWidth],
                    [238, $activityBoxHeight],
                    $border,
                    $borderWidth);
                $this->addOutlineRectangle([296, $offsetHeight + $maturityLevelBoxHeight - $borderWidth],
                    [239, $activityBoxHeight],
                    $border,
                    $borderWidth);
            } else {
                $this->pdf->startTransaction();
            }
            $maxY = 0;
            foreach ($activities as $index => $activity) {
                $text = $activity->getShortDescription();
                $this->addTextField(
                    $text,
                    [
                        60 + $streamDescriptionOffsets[$index][0],
                        $offsetHeight + $maturityLevelBoxHeight - $borderWidth + $streamDescriptionOffsets[$index][1],
                    ],
                    'L',
                    $activityDescriptionWidth
                );
                $maxY = max($this->pdf->GetY(), $maxY);
            }
            $activityBoxHeight = $maxY - $offsetHeight - $maturityLevelBoxHeight + $boxMargin;

            if (!$real) {
                $this->pdf->rollbackTransaction(true);
            }
        }

        return $offsetHeight + $maturityLevelBoxHeight - $borderWidth + $activityBoxHeight;
    }

    private function printMaturityLevelCircles($startCoordinates, $level, $color): void
    {
        $circlesSpacing = 2;
        $circlesRadius = 7;
        $circleDefaultColor = self::COLOR_VERY_LIGHT_GREY;
        for ($i = 0; $i < 3; $i++) {
            $fill = ($i < $level) ? $color : $circleDefaultColor;
            $this->pdf->Circle(
                $startCoordinates[0] + $circlesRadius + ($i * (2 * $circlesRadius + $circlesSpacing)),
                $startCoordinates[1] + $circlesRadius,
                $circlesRadius,
                0,
                360,
                'F',
                [],
                $fill
            );
        }
    }

    private function getPracticeActivitiesPerLevel(Practice $practice): array
    {
        $levelActivities = [];
        $streams = $this->getOrderedPracticeStreams($practice);
        for ($i = 0; $i < 3; $i++) {
            $activities = array_map(
                fn(Stream $stream) => array_values(
                    array_filter(
                        $stream->getStreamActivities()->getValues(),
                        fn(Activity $activity) => $activity->getPracticeLevel()->getMaturityLevel()->getLevel() === $i + 1
                    )
                )[0],
                $streams
            );
            /** @var Activity[] $activities */
            usort(
                $activities,
                fn(Activity $a, Activity $b) => (int)($a->getPracticeLevel()->getMaturityLevel()->getLevel() > $b->getPracticeLevel(
                    )->getMaturityLevel()->getLevel())
            );
            $levelActivities[$i] = $activities;
        }

        return $levelActivities;
    }

    /**
     * @param Practice $practice
     * @return Stream[]
     */
    private function getOrderedPracticeStreams(Practice $practice): array
    {
        $streams = $practice->getPracticeStreams()->getValues();
        /** @var Stream[] $streams */
        usort($streams, fn(Stream $a, Stream $b) => (int)($a->getOrder() > $b->getOrder()));

        return $streams;
    }

    private function addSecurityPracticePage2(Practice $practice, array $levelActivities, $color): void
    {
        $this->addSecurityPracticeTemplate($practice, $color, "Streams Overview");

        $bigPad = 23;
        $smallPad = 9;

        $offsetHeight = $this->pdf->GetY() + $bigPad;

        $streams = $this->getOrderedPracticeStreams($practice);
        foreach ($streams as $stream) {
            $this->setFontOpensansSemi(17);
            $text = "Stream ".$stream->getLetter()." - ".$this->getStreamName($stream);
            $this->addTextField($text, [60, $offsetHeight]);
            $offsetHeight = $this->pdf->GetY() + $smallPad;
            $this->setDefaultFont();
            $text = $stream->getDescription();
            $this->addTextField($text, [60, $offsetHeight]);
            $offsetHeight = $this->pdf->GetY() + $bigPad;
        }
    }


    private function addSecurityPracticePage3(Practice $practice, array $levelActivities, $color): void
    {
        $this->addSecurityPracticeTemplate($practice, $color, "Activities Overview");

        $borderWidth = 2;
        $style = ['width' => $borderWidth, 'color' => $color];
        $border = ['all' => $style];

        $bigPad = 23;
        $smallPad = 14;

        $offsetHeight = $this->pdf->GetY() + $bigPad;

        $this->addOutlineRectangle([60, $offsetHeight], [238, 60], $border, $borderWidth);
        $this->addOutlineRectangle([296, $offsetHeight], [239, 60], $border, $borderWidth);

        $streams = $this->getOrderedPracticeStreams($practice);

        $this->setFontOpensans(10, self::COLOR_CHARCOAL, 1.4);
        $text = "Stream A";
        $this->addTextField($text, [75, $offsetHeight + $smallPad]);
        $text = "Stream B";
        $this->addTextField($text, [311, $offsetHeight + $smallPad]);

        $this->setFontOpensansSemi(13);
        $text = $this->getStreamName($streams[0]);
        $this->addTextField($text, [74, $offsetHeight + 2 * $smallPad]);

        $text = $this->getStreamName($streams[1]);
        $this->addTextField($text, [310, $offsetHeight + 2 * $smallPad]);

        $offsetHeight = $this->pdf->GetY() + $bigPad + $smallPad;

        for ($i = 0; $i < 3; $i++) {
            $offsetHeight = $this->printMaturityLevel($levelActivities[$i], $offsetHeight, $border, $borderWidth, $color);
            $offsetHeight += $bigPad;
        }
    }

    /**
     * @param Activity[] $activities
     * @return void
     */
    private function addActivityPages(array $activities, $color): void
    {
        $stream = $activities[0]->getStream();
        $this->pdf->Bookmark("Stream ".$stream->getLetter()." - ".$this->getStreamName($stream), 1, 0, $this->pdf->getPage() + 1, '', $color);

        foreach ($activities as $activity) {
            $this->addActivityPage1($activity, $color);
            $this->addActivityPage2($activity, $color);
        }
    }

    private function addActivityTemplate(Activity $activity, $color): void
    {
        $this->pdf->AddPage('P');


        $level = $activity->getPracticeLevel()->getMaturityLevel()->getLevel();
        $stream = $activity->getStream();
        $practice = $stream->getPractice();
        $businessFunction = $practice->getBusinessFunction();

        $this->addPageHeader($this->getPracticeName($practice), self::COLOR_WHISPER, $color);

        $this->addPageTitle("Stream ".$stream->getLetter()." - ".$this->getStreamName($stream), [60, 150], $color);


        $this->addBusinessFunctionName($businessFunction->getName(), [581, 106], $color);

        $this->printMaturityLevelCircles([163, 202], $level, $color);

        $this->setDefaultFont();

        $text = "Maturity level $level";
        $this->addTextField($text, [60, 200]);
    }

    private function addActivityPage1(Activity $activity, $color): void
    {
        $this->addActivityTemplate($activity, $color);
        $level = $activity->getPracticeLevel()->getMaturityLevel()->getLevel();
        $this->pdf->Bookmark("Maturity level $level", 2, 0, '', '', $color);

        $this->setFontOpensans(17);

        $text = "Benefit";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setDefaultFont();
        $txt = $activity->getBenefit();
        $this->addTextField($txt, [60, $this->pdf->GetY() + 9]);

        $this->setFontOpensans(17);
        $text = "Activity";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setDefaultFont();


        // This might not work as expected
        $text = str_replace('*', ' •  ', $activity->getLongDescription());
        $text = str_replace('`', '\'', $text);
        $paragraphs = [];
        preg_match_all('/.+/', $text, $paragraphs);
        $paragraphs = reset($paragraphs);
        $this->activityRecursiveCuntinuation($activity, $color, $paragraphs);
    }

    private function activityRecursiveCuntinuation(Activity $activity, $color, $paragraphs)
    {
        $overflow = true;
        $cut = 0;
        while ($overflow) {
            $firstParagraphs = array_slice($paragraphs, 0, sizeof($paragraphs) - $cut);
            $txt = implode("\n\n", $firstParagraphs);
            $this->pdf->startTransaction();
            $startPage = $this->pdf->getPage();
            $this->addTextField($txt, [60, $this->pdf->GetY() + 14]);
            $overflow = ($this->pdf->getPage() != $startPage);
            $this->pdf->rollbackTransaction(true);

            $cut += $overflow;
        }

        $this->addTextField($txt, [60, $this->pdf->GetY() + 14]);

        if ($cut) {
            $secondParagraphs = array_slice($paragraphs, sizeof($paragraphs) - $cut, $cut);
            $this->addActivityPage1Continued($activity, $color, $secondParagraphs);
        }
    }

    private function addActivityPage1Continued(Activity $activity, $color, $paragraphs): void
    {
        $this->addActivityTemplate($activity, $color);

        $this->setFontOpensans(17);
        $text = "Activity (continued)";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setDefaultFont();
        $this->activityRecursiveCuntinuation($activity, $color, $paragraphs);
    }

    private function getQualityCriteriaFormatted(Question $question): string
    {
        $result = "<ul>";
        foreach (explode("\n", $question->getQuality()) as $index => $quality) {
            $result .= ($index) ? "<br>" : "";
            $result .= "<li>$quality</li>";
        }
        $result .= "</ul>";

        return $result;
    }


    private function addActivityPage2(Activity $activity, $color)
    {
        $this->addActivityTemplate($activity, $color);
        $this->pdf->setAutoPageBreak(true, 25);

        $borderWidth = 2;
        $style = ['width' => $borderWidth, 'color' => $color];
        $border = ['all' => $style];

        $offsetWidth = 60;
        $offsetHeight = 242;
        $boxMargin = 14;
        $interBoxesMargin = 23;

        $rectangleWidth = 475;
        $titleHeight = 36;

        $questionHeight = 0;
        foreach ([false, true] as $real) {
            if ($real) {
                $this->addOutlineRectangle([$offsetWidth, $offsetHeight], [$rectangleWidth, $questionHeight], $border, $borderWidth);
            } else {
                $this->pdf->startTransaction();
            }
            $this->setDefaultFont();
            /** @var Question $question */
            $question = array_values($activity->getActivityQuestions()->getValues())[0];
            $text = $question->getText();
            $this->addTextField($text, [$offsetWidth + $boxMargin, $offsetHeight + $boxMargin], 'L', $rectangleWidth - 2 * $boxMargin);

            $questionHeight = $this->pdf->GetY() - $offsetHeight + $boxMargin;

            if (!$real) {
                $this->pdf->rollbackTransaction(true);
            }
        }
        $offsetHeight += $questionHeight + $interBoxesMargin;

        $this->addOutlineRectangle([$offsetWidth, $offsetHeight], [$rectangleWidth, $titleHeight], $border, $borderWidth, $color);
        $this->setFontOpensansSemi(13, self::COLOR_WHISPER);
        $text = "Quality Criteria";
        $this->addTextField($text, [$offsetWidth, $offsetHeight], 'L', $rectangleWidth, $titleHeight, false, [14, 9, 14, 9], "M");
        $offsetHeight += $titleHeight - $borderWidth;

        $qualityCriteriaHeight = 0;
        foreach ([false, true] as $real) {
            if ($real) {
                $this->addOutlineRectangle([$offsetWidth, $offsetHeight], [$rectangleWidth, $qualityCriteriaHeight], $border, $borderWidth);
            } else {
                $this->pdf->startTransaction();
            }

            $this->setFontOpensans(10, self::COLOR_CHARCOAL, 1.4);

            $text = $this->getQualityCriteriaFormatted($question);
            $this->addTextField(
                $text,
                [$offsetWidth + $boxMargin, $offsetHeight + $boxMargin],
                'L',
                $rectangleWidth - 2 * $boxMargin,
                0,
                true,
                [6, 0, 14, 0]
            );

            $qualityCriteriaHeight = $this->pdf->GetY() - $offsetHeight + $boxMargin;

            if (!$real) {
                $this->pdf->rollbackTransaction(true);
            }
        }
        $offsetHeight += $qualityCriteriaHeight + $interBoxesMargin;

        $this->addOutlineRectangle([$offsetWidth, $offsetHeight], [$rectangleWidth, $titleHeight], $border, $borderWidth, $color);
        $this->setFontOpensansSemi(13, self::COLOR_WHISPER);
        $text = "Answers";
        $this->addTextField($text, [$offsetWidth, $offsetHeight], 'L', $rectangleWidth, $titleHeight, false, [14, 9, 14, 9], "M");
        $offsetHeight += $titleHeight - $borderWidth;

        $questionAnswers = $question->getAnswerSet()->getAnswerSetAnswers()->getValues();
        /** @var Answer[] $questionAnswers */
        usort($questionAnswers, fn(Answer $a, Answer $b) => (int)($a->getOrder() > $b->getOrder()));

        $this->setFontOpensans(10, self::COLOR_CHARCOAL, 1.4);

        /** @var Answer $answer */
        foreach ($questionAnswers as $answer) {
            $text = $answer->getText();
            $this->addOutlineRectangle([$offsetWidth, $offsetHeight], [$rectangleWidth, $titleHeight], $border, $borderWidth);
            $this->addTextField($text, [$offsetWidth, $offsetHeight], 'L', $rectangleWidth, $titleHeight, false, [14, 9, 14, 9], "M");
            $offsetHeight += $titleHeight - $borderWidth;
        }
        $this->pdf->setAutoPageBreak(true, 60);
    }

    private function addCreditsPage(): void
    {
        $this->pdf->AddPage('P');
        $this->addPageHeader("Credits", self::COLOR_WHISPER, self::COLOR_GLACIER);

        $title = "OWASP SAMM Team and Contributors";
        $this->addPageTitle($title, [60, 120], self::COLOR_GLACIER);
        $this->pdf->Bookmark($title, 1, 0, $this->pdf->getPage(), '', self::COLOR_MAROON);


        $this->setDefaultFont();
        $text = "The OWASP SAMM project is powered by the work of its team and contributors. We would like to thank everyone who made this possible.";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setFontOpensansSemi(17);
        $text = "Project Leaders";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setDefaultFont();
        $text = " •  Sebastien Deleersnyder\n •  Bart De Win";
        $this->addTextField($text, [60, $this->pdf->GetY() + 9]);

        $this->setFontOpensansSemi(17);
        $text = "SAMM version 2 contributors";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setDefaultFont();
        $height = $this->pdf->GetY() + 9;
        $text = " •  Sebastian Arriada\n •  Maxim Baele\n •  Chris Cooper \n •  Brett Crawley\n •  Patricia Duarte\n •  John DiLeo\n •  John Ellingsworth\n •  Brian Glas\n •  Aram Hovsepyan";
        $this->addTextField($text, [60, $height]);
        $maxY = $this->pdf->GetY();

        $text = " •  Bruce Jenkins\n •  Nessim Kisserli\n •  Daniel Kefer\n •  John Kennedy\n •  Yan Kravchenko\n •  Timo Pagel\n •  Hardik Parekh\n •  Felipe Zipitria";
        $this->addTextField($text, [($this->pdf->getPageWidth() + 33) / 2, $height]);
        $maxY = max($maxY, $this->pdf->GetY());

        $text = "There are many names that are not on the list but contributed with their input, feedback, or fixes. We apppreciate you all!\nIf you know of someone you think should be on the list, let us know.";
        $this->addTextField($text, [60, $maxY + 37]);
    }

    private function addSponsorsPage(): void
    {
        $this->pdf->AddPage('P');
        $this->addPageHeader("Sponsors", self::COLOR_WHISPER, self::COLOR_GLACIER);

        $title = "Sponsoring an OWASP Flagship Project";
        $this->addPageTitle($title, [60, 120], self::COLOR_GLACIER);
        $this->pdf->Bookmark($title, 1, 0, $this->pdf->getPage(), '', self::COLOR_MAROON);

        $this->setDefaultFont();
        $text = "By sponsoring SAMM, you support a Flagship OWASP project. The OWASP Flagship designation is given to projects that have demonstrated strategic value to OWASP and application security as a whole.";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setFontOpensansSemi(17);
        $text = "Proceeds";
        $this->addTextField($text, [60, $this->pdf->GetY() + 23]);

        $this->setDefaultFont();
        $text = "All proceeds from the sponsorship support the mission of the OWASP Foundation and the further development of SAMM, funding\n";
        $this->addTextField($text, [60, $this->pdf->GetY() + 9]);

        $this->setDefaultFont();
        $text = " •  marketing & PR support\n •  technical editing & UX support\n •  website development and hosting\n •  SAMM participation in the Open Security Summit\n •  core team summits\n •  tooling for the SAMM Benchmark project";
        $this->addTextField($text, [60, $this->pdf->GetY()]);

        $basePath = $this->parameterBag->get('kernel.project_dir').'/SAMM-PDF/images/sponsors/';
        $logos = array_values(array_filter(scandir($basePath), fn($item) => ($item[0] !== '.')));
        $logosPerLine = 4;
        $logoSize = 90;
        foreach ($logos as $index => $logo) {
            $x = (float)(104 + ($logoSize + 9) * ((int)$index % $logosPerLine));
            $y = (float)($this->pdf->GetY() + 8 + $logoSize * intdiv($index, $logosPerLine));
            $this->pdf->Image(
                $basePath.$logo,
                $x,
                $y,
                $logoSize,
                $logoSize
            );
        }
        $this->setDefaultFont();
        $text = "For more information visit www.owaspsamm.org/sponsors.";
        $this->addTextField($text, [60, $this->pdf->GetY() + 3 * 90 + 10]);
    }

    private function addLicensePage(): void
    {
        $this->pdf->AddPage('P');
        $this->addPageHeader("License", self::COLOR_WHISPER, self::COLOR_GLACIER);

        $title = "OWASP SAMM Publishing License";
        $this->addPageTitle($title, [60, 120], self::COLOR_GLACIER);
        $this->pdf->Bookmark($title, 1, 0, $this->pdf->getPage(), '', self::COLOR_MAROON);

        $this->setDefaultFont();
        $text = "OWASP SAMM is published under the CC BY-SA 4.0 license.";
        $this->addTextField($text, [60, $this->pdf->GetY() + 12]);
    }
}
