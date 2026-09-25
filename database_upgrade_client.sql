-- Legacy PostgreSQL upgrade for an existing installation.
-- New deployments should run supabase_schema.sql instead.
ALTER TABLE public.content_requests ADD COLUMN IF NOT EXISTS user_id bigint;
DO $$ BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'content_requests_user_id_fkey') THEN
        ALTER TABLE public.content_requests
        ADD CONSTRAINT content_requests_user_id_fkey FOREIGN KEY (user_id)
        REFERENCES public.users(id) ON DELETE SET NULL;
    END IF;
END $$;
