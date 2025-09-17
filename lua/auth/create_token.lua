-- KEYS[1]: token_key
-- ARGV[1]: user_id, ARGV[2]: token, ARGV[3]: ttl (сек)
redis.call('SET', KEYS[1], ARGV[1], 'EX', ARGV[3])
return 1