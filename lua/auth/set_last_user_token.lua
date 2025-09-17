-- KEYS[1]: token_key
-- ARGV[1]: token, ARGV[2]: ttl (сек)
redis.call('SET', KEYS[1], ARGV[1], 'EX', ARGV[2])
return 1