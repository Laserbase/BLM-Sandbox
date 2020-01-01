# BLM-Sandbox
Sandbox to investigate parsing BLM/csv in low memory conditions

# Initial Illuminate/Pipeline/Pipeline not a solution
No advantage over using file_get_contents() and parsing contents.

Instead using PHP gererator function to split processing to discrete chunks so that only the current row is in memory.

# 2020-01-01 Fix enum index if numeric has leading zeros
- Change isEnum to ensure leading zeros do not affect index resolution, length checks still hold
- Add CircleCi badge to readme.md

# 2019-12-30 Check PROP_SUB_ID
- Add test for PROP_SUB_ID
- Add test fot int
- Add test for num

# 2019-12-30 Check date fields
- add test for date columns created, updated, let-date-available

# 2019-12-28 Add testing for enums
- Add test for enums such as STATUS_ID

# 2019-12-24 Allow reading of #HEADER# items to be in any order
- Add test to ensure header items are no longer in a stipulated order

# 2019-12-24 Set LET_TYPE_ID to required as per spec
- Change assertions to catch missing required field

# 2019-12-23 Check student letting only appear if (LET_TYPE_ID === 3)
- Add check for letting columns only have values if the correct LET_TYPE_ID is set

# 2019-12-22 Check that EPC and HIP graphics are in correct index
- Add test for caption is not 'EPC' or 'HIP' for property images
- Add test for correct caption for 'ERP' or 'EPC' certificates
- Add test that each certificate has a caption
- Add test for all MEDIA fields MUST appear after all other fields

# 2019-12-20 Check media filenames
- Add test media files are in the format <AGENT_REF>_<MEDIATYPE>_<n>.<file extension>

# 2019-1219 Create HISTORY.md from README.md
- Add test for '\' behaviour before End-of-Field marker
- Add row check for each 'media-text' column has a matching 'media' column

# 2019-12-19 Clean up comments
- Next stage finish inter-field dependencies
- Move this to history
- Create real README

# 2019-12-03 Only test available due to example tests being flawed
```vendor/bin/phpunit --filter test_columnDefinitionsTest -vvv```


# 2019-12-01 @Todo
Duplicate tests using folder instead of using the zip archive 'tests/files/141212100024_FBM_2014120711.zip'
*    which should not be altered.
*    Create test file from StartupTest as initial test.

Drop the memory down to a low level.
*   To force low memory conditions.

Add processing to cope with rows having fields that contain new-line, carriage-return, line-feed characters.

Split monolith into columns and rules.

Allow processing to be relaxed so that the level of strictness can be chosen.

For each rule in the specification write a test, and implement rules in BlmFile.
*    Look for anomolies such as MEDIA_IMAGE_TEXT_60 having a size of 20 characters but only allowing the contents to be the 3 character string 'EPC'. 
