# BLM-Sandbox
Sandbox to investigate parsing BLM/csv in low memory conditions

# Initial Illuminate/Pipeline/Pipeline not a solution
No advantage over using file_get_contents() and parsing contents.

Instead using PHP gererator function to split processing to discrete chunks so that only the current row is in memory.

