<?php

namespace Domain\Ticket\Actions\Report;

use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\TaskCompletedReport;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\Ticket\Models\TicketReport;
use Exception;
use Illuminate\Support\Facades\File;
use KoalaFacade\DiamondConsole\Foundation\Action;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

readonly class GenerateReportDocumentAction extends Action
{
    public function execute(Ticket $ticket): void
    {
        $phpWord = new PhpWord();
        \PhpOffice\PhpWord\Settings::setZipClass(\PhpOffice\PhpWord\Settings::PCLZIP);

        $section = $phpWord->addSection();

        $standardTextStyle = [
            'bold' => false,
            'name' => 'Calibri (Body)',
            'size' => 12
        ];
        $center = $phpWord->addParagraphStyle('p2StyleCenter', ['align'=>'center','marginTop' => 1]);
        $left = $phpWord->addParagraphStyle('p2StyleLeft', ['align'=>'left','marginTop' => 1]);

        // Header Logo
        $header = $section->addHeader();
        $section->addImage(public_path('images/dr-demo.png'), ['align'=>'right', 'topMargin' => -5, 'width' => 120]);

        // Report Title
        $section->addText('Maintenance Report', ['bold' => true,'name'=>'Calibri (Body)','size' => 14], $center);
        
        $section->addTextBreak(1);

        // Ticket Info Table
        $tableStyle = [
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
            'afterSpacing' => 0,
            'Spacing'=> 0,
            'cellMargin'=>0
        ];
        $cellStyle = [
            'borderTopColor' =>'FFFFFF',
            'borderTopSize' => 6,
            'borderRightColor' =>'FFFFFF',
            'borderRightSize' => 6,
            'borderBottomColor' =>'FFFFFF',
            'borderBottomSize' => 6,
            'borderLeftColor' =>'FFFFFF',
            'borderLeftSize' => 6,
        ];

        $phpWord->addTableStyle('ticketTable', $tableStyle);
        $table = $section->addTable('ticketTable');

        // First Row
        $table->addRow(1);
        $table->addCell(2500, $cellStyle)->addText('Ticket Date', $standardTextStyle, $left);
        $table->addCell(500, $cellStyle)->addText(':', $standardTextStyle, $left);
        $table->addCell(3000, $cellStyle)->addText($ticket->created_at->format('d F Y'), $standardTextStyle, $left);
        $table->addCell(2500, $cellStyle)->addText('', $standardTextStyle, $left);
        $table->addCell(2500, $cellStyle)->addText('Ticket Number', $standardTextStyle, $left);
        $table->addCell(500, $cellStyle)->addText(':', $standardTextStyle, $left);
        $table->addCell(3000, $cellStyle)->addText($ticket->ticket_number, $standardTextStyle, $left);

        // Second Row
        $table->addRow(1);
        $table->addCell(2500, $cellStyle)->addText('Title', $standardTextStyle, $left);
        $table->addCell(500, $cellStyle)->addText(':', $standardTextStyle, $left);
        $table->addCell(3000, $cellStyle)->addText($ticket->subject, $standardTextStyle, $left);
        $table->addCell(2500, $cellStyle)->addText('', $standardTextStyle, $left);
        $table->addCell(2500, $cellStyle)->addText('', $standardTextStyle, $left);
        $table->addCell(500, $cellStyle)->addText('', $standardTextStyle, $left);
        $table->addCell(3000, $cellStyle)->addText('', $standardTextStyle, $left);

        // Third Row
        $table->addRow(1);
        $table->addCell(2500, $cellStyle)->addText('Ticket Descriptions', $standardTextStyle, $left);
        $table->addCell(500, $cellStyle)->addText(':', $standardTextStyle, $left);
        $table->addCell(3000, $cellStyle)->addText($ticket->description, $standardTextStyle, $left);
        $table->addCell(2500, $cellStyle)->addText('', $standardTextStyle, $left);
        $table->addCell(2500, $cellStyle)->addText('Task Number', $standardTextStyle, $left);
        $table->addCell(500, $cellStyle)->addText(':', $standardTextStyle, $left);

        $taskNumberCell = $table->addCell(3000, $cellStyle);

        $lineCount = round (count($ticket->tasks)/4);

        $lastTaskIndex = 0;

        for ($i = 0; $i < $lineCount; $i++) {
            $text = '';
            for ($j = 0; $j < 4; $j++) {
                if ($lastTaskIndex < count ($ticket->tasks)) {
                    $text .= $ticket->tasks[$lastTaskIndex]->task_number;
                    
                    if ($j != count($ticket->tasks)-1) {
                        $text .= ',';
                    }
    
                    $lastTaskIndex+=1;
                }
            }

            $taskNumberCell->addText($text, $standardTextStyle, $left);
        }

        $section->addTextBreak(1);

        // Task Info Table
        $tableStyle = [
            'borderSize' => 1,
            'borderColor' => '00000',
            'afterSpacing' => 0,
            'Spacing' => 0,
            'cellMargin' => 0
        ];
        $phpWord->addTableStyle('taskTable', $tableStyle);
        $table = $section->addTable('taskTable');
        $table->addRow(1);
        $table->addCell(5000)->addText('Task Descriptions', [
            'bold' => true,
            'name' => 'Calibri (Body)',
            'size' => 12
        ], $center);
        $table->addCell(5000)->addText('Action for Issue', [
            'bold' => true,
            'name' => 'Calibri (Body)',
            'size' => 12
        ], $center);

        foreach ($ticket->tasks as $key => $task) {
            // Descriptions Cell
            $table->addRow(1);
            $cell1 = $table->addCell(5000);
            $cell1->addText($key+1 . ') ' . $task->task_number . ' - ' . $task->title, ['bold' => true]);
            $cell1->addTextBreak(0.5);
            $cell1->addText($task->description);
            $cell1->addTextBreak(1);

            $taskImages = $task->getMedia(Task::COLLECTION_NAME);

            foreach ($taskImages as $image) {
                $imgPath = public_path('storage/' . $image->id . '/' . $image->file_name);

                if (File::exists($imgPath)) {
                    $cell1->addImage($imgPath, ['align'=>'left', 'leftMargin' => 10, 'width' => 150]);
                }
            }

            // Action Cell
            $cell2 = $table->addCell(5000);
            // Completed Report Row
            foreach ($task->completedReports as $completedReport) {
                $cell2->addText($completedReport->notes);

                $cell2->addTextBreak(0.5);

                $reportImages = $completedReport->getMedia(TaskCompletedReport::COLLECTION_NAME);

                foreach ($reportImages as $image) {
                    $imgPath = public_path('storage/' . $image->id . '/' . $image->file_name);

                    if (File::exists($imgPath)) {
                        $cell2->addImage($imgPath, ['align'=>'left', 'leftMargin' => 10, 'width' => 150]);
                    }
                }

                $cell2->addTextBreak(1);
            }

            $cell2->addTextBreak(1);

            // Costs Row
            $cell2->addText('Costs', ['bold' => true]);
            $cell2->addTextBreak(0.5);
            foreach ($task->costs as $index => $cost) {
                $cell2->addText($cost->description . ' - RM ' . $cost->cost);
                $cell2->addTextBreak(0.5);
            }
        }

        $section->addTextBreak(1);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        try {
            $objWriter->save(storage_path($ticket->ticket_number . '.docx'));

            $report = TicketReport::query()
                ->create([
                    'ticket_id' => $ticket->id,
                    'created_by' => 1,
                    'title' => 'Auto Generated',
                    'description' => 'Auto Generated Report Document for Ticket Number ' . $ticket->ticket_number,
                    'is_generated' => 1
                ]);

            $report->addMedia(storage_path($ticket->ticket_number . '.docx'))
                ->toMediaCollection(TicketReport::COLLECTION_NAME);
            
            unlink(storage_path($ticket->ticket_number . '.docx'));
        } catch (Exception $e) {
        }
    }
}