<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class PdfQuestionParser
{
    private $text;
    private $questions = [];
    private $currentOrder = 1;

    public function parse(string $filePath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $this->text = $pdf->getText();
        
        $this->questions = [];
        $this->currentOrder = 1;
        
        $this->text = $this->normalizeText($this->text);
        $this->parseSections();
        
        return $this->questions;
    }

    private function normalizeText(string $text): string
    {
        $text = preg_replace('/\r\n/', "\n", $text);
        $text = preg_replace('/\r/', "\n", $text);
        
        $lines = explode("\n", $text);
        $normalizedLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            $line = preg_replace('/\s+/', ' ', $line);
            $normalizedLines[] = $line;
        }
        
        return implode("\n", $normalizedLines);
    }

    private function parseSections(): void
    {
        $lines = explode("\n", $this->text);
        $currentType = 'multiple_choice';
        $currentQuestion = null;
        $readingAnswer = false; // ✅ Flag untuk multi-line answer
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // 1. Deteksi header tipe soal (reset readingAnswer)
            $detectedType = $this->detectType($line);
            if ($detectedType) {
                if ($currentQuestion) {
                    $this->finalizeQuestion($currentQuestion);
                    $this->questions[] = $currentQuestion;
                    $currentQuestion = null;
                }
                $currentType = $detectedType;
                $readingAnswer = false;
                continue;
            }
            
            // 2. Deteksi nomor soal baru (reset readingAnswer)
            if (preg_match('/^(\d+)[\.\)]\s+(.+)$/i', $line, $matches)) {
                if ($currentQuestion) {
                    $this->finalizeQuestion($currentQuestion);
                    $this->questions[] = $currentQuestion;
                }
                
                $currentQuestion = [
                    'order_number' => $this->currentOrder++,
                    'question_type' => $currentType,
                    'question_text' => trim($matches[2]),
                    'options' => [],
                    'correct_answer' => '',
                    'points' => 1,
                    'explanation' => ''
                ];
                $readingAnswer = false;
                continue;
            }
            
            // 3. Deteksi pilihan jawaban (a. b. c. d. e.)
            if ($currentQuestion && !$readingAnswer && preg_match('/^([a-e])[\.\)]\s+(.+)$/i', $line, $matches)) {
                $currentQuestion['options'][] = trim($matches[2]);
                continue;
            }
            
            // 4. ✅ Deteksi jawaban (support multi-line untuk essay)
            if ($currentQuestion && preg_match('/^(JAWAB|JAWABAN|JAWABAN BENAR|ANSWER|CORRECT ANSWER|KUNCI|KUNCI JAWABAN|KUNCI JAWABAN BENAR|KUNCI JAWAB)\s*[:\.]\s*(.*)$/i', $line, $matches)) {
                $currentQuestion['correct_answer'] = trim($matches[2]);
                $readingAnswer = true; // ✅ Mulai mode multi-line
                continue;
            }
            
            // 5. Deteksi explanation (reset readingAnswer)
            if ($currentQuestion && preg_match('/^(PEMBAHASAN|EXPLANATION)\s*[:\.]\s*(.+)$/i', $line, $matches)) {
                $currentQuestion['explanation'] = trim($matches[2]);
                $readingAnswer = false;
                continue;
            }
            
            // 6. ✅ Jika sedang membaca jawaban multi-line, tambahkan ke jawaban
            if ($currentQuestion && $readingAnswer) {
                // Stop jika ketemu pattern yang menandakan akhir jawaban
                if ($this->isAnswerEndMarker($line)) {
                    $readingAnswer = false;
                    // Jangan continue, biarkan line ini diproses di step berikutnya
                } else {
                    // Tambahkan ke jawaban (dengan spasi untuk essay)
                    if (!empty($currentQuestion['correct_answer'])) {
                        $currentQuestion['correct_answer'] .= ' ' . $line;
                    } else {
                        $currentQuestion['correct_answer'] = $line;
                    }
                    continue;
                }
            }
            
            // 7. Jika masih dalam soal, tambahkan ke text pertanyaan
            if ($currentQuestion && !$readingAnswer) {
                $currentQuestion['question_text'] .= ' ' . $line;
            }
        }
        
        // Simpan soal terakhir
        if ($currentQuestion) {
            $this->finalizeQuestion($currentQuestion);
            $this->questions[] = $currentQuestion;
        }
    }

    /**
     * ✅ Deteksi penanda akhir jawaban (untuk multi-line)
     */
    private function isAnswerEndMarker(string $line): bool
    {
        if (preg_match('/^\d+[\.\)]\s+/', $line)) return true;
        if ($this->detectType($line)) return true;
        if (preg_match('/^(JAWAB|PEMBAHASAN|EXPLANATION|KUNCI|ANSWER)\s*[:\.]/i', $line)) return true;
        if (preg_match('/^[a-e][\.\)]\s+/i', $line)) return true;
        
        return false;
    }

    private function finalizeQuestion(array &$question): void
    {
        $question['question_text'] = trim(preg_replace('/\s+/', ' ', $question['question_text']));
        
        // Untuk essay, pertahankan struktur paragraf
        if ($question['question_type'] === 'essay') {
            $question['correct_answer'] = trim($question['correct_answer']);
        } else {
            $question['correct_answer'] = trim(preg_replace('/\s+/', ' ', $question['correct_answer']));
        }
        
        // Proses berdasarkan tipe soal
        if ($question['question_type'] === 'true_false') {
            $question['options'] = ['True', 'False'];
            $answer = strtolower(trim($question['correct_answer']));
            if (in_array($answer, ['true', 'benar', 'b', '1', 'yes', 't'])) {
                $question['correct_answer'] = 'True';
            } else {
                $question['correct_answer'] = 'False';
            }
        }
        
        if ($question['question_type'] === 'multiple_choice' && !empty($question['options'])) {
            $answer = trim($question['correct_answer']);
            if (preg_match('/^[a-e]$/i', $answer)) {
                $index = ord(strtolower($answer)) - ord('a');
                if (isset($question['options'][$index])) {
                    $question['correct_answer'] = $question['options'][$index];
                }
            }
        }
    }

    private function detectType(string $line): ?string
    {
        $line = strtoupper(trim($line));
        $line = preg_replace('/[^A-Z\s\/]/', '', $line);
        $line = trim($line);
        
        if (preg_match('/^(PG|PILIHAN GANDA|MULTIPLE CHOICE|PILIHAN)$/i', $line)) {
            return 'multiple_choice';
        }
        if (preg_match('/^(BENAR SALAH|TRUE FALSE|BENAR\/SALAH|TRUE\/FALSE|B\/S|T\/F|BENAR-SALAH)$/i', $line)) {
            return 'true_false';
        }
        if (preg_match('/^(JAWABAN SINGKAT|SHORT ANSWER|ISIAN|ISIAN SINGKAT|JAWAB SINGKAT)$/i', $line)) {
            return 'short_answer';
        }
        if (preg_match('/^(ESSAY|URAIAN|ESAI)$/i', $line)) {
            return 'essay';
        }
        
        return null;
    }
}