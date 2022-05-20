# coingeckoapicache
A own cache system for coingecko API to reduce demand rate (and provide cached results in case of error)

* change / add any specific API urls that you want to perform additional checks on
* You can 'seed' cache (xxxxxx) file if its currently failing (on first attempt).
* Determine hash from cache/xxxxxx.err and .log files from failed API read
* Open in browser with javascript enabled and copy paste resultant raw json code into above cache/xxxxxx
