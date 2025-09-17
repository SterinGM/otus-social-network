-- KEYS[1]: token_key
local user_id = redis.call('GET', KEYS[1])
if not user_id then
    return nil
end
return user_id