Evaluate this APTIS Writing Part {{ $part }} response from a {{ $targetLevel ?? 'B2' }} level student.

---
@if($part == 1 && !empty($metadata['fields']))
## Questions (Part 1 – Short Answers):
@foreach($metadata['fields'] as $idx => $field)
- Câu {{ $idx + 1 }}: {{ $field['label'] ?? '' }}
@endforeach

@elseif($part == 2)
## Task (Part 2 – Short Text):
Scenario: {{ $metadata['scenario'] ?? $question }}
@if(!empty($metadata['hints']))
Hints / Notes: {{ $metadata['hints'] }}
@endif
Word limit: 20–30 words.

@elseif($part == 3 && !empty($metadata['questions']))
## Social Media Posts to Reply To (Part 3):
@foreach($metadata['questions'] as $idx => $pq)
- Post {{ $idx + 1 }}: {{ $pq['prompt'] ?? '' }}
@endforeach
Each reply: 30–40 words. Natural, friendly, online-style tone.

@elseif($part == 4)
## Email Writing Task (Part 4):
Context / Situation: {{ $metadata['context'] ?? $question }}
@if(!empty($metadata['email']['body']))
Email received:
---
{{ $metadata['email']['body'] }}
---
@endif
- Task 1 (Informal – ~50 words): {{ $metadata['task1']['instruction'] ?? '' }}
- Task 2 (Formal – 120–150 words): {{ $metadata['task2']['instruction'] ?? '' }}
@endif

---
## Student Response:
{{ $studentText }}

---
Now evaluate the above response strictly following the JSON schema and rubric provided in the system prompt.
Return the JSON result only.
