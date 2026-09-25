<?php
http_response_code(410);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Legacy MySQL upgrade endpoint is disabled. Use supabase_schema.sql in Supabase SQL Editor.';
