-- KEYS[1]: token_key
redis.call('DEL', KEYS[1])
return 1