-- KEYS[1]: token_key
local token = redis.call('GET', KEYS[1])
if not token then
    return nil
end
return token