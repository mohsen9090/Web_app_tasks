#!/bin/bash
# ایجاد توضیح تصادفی 
# برای تسک
TASK_DESCRIPTION=$(shuf -n 1 -e "Go to Paris" 
"Buy groceries" "Meeting with John" "Wash the 
dishes" "Walk the dog" "Read a book")
# ایجاد اولویت تصادفی 
# (بین 1 و 3)
PRIORITY=$((RANDOM % 3 + 1))
# ارسال درخواست POST به API
curl -X POST "http://gold24.io:3006/api/tasks" \ 
-H "Content-Type: application/json" \
-d "{\"task_description\": \"$TASK_DESCRIPTION\", \"priority\": $PRIORITY}"
