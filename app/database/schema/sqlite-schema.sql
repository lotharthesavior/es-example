CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "stored_events"(
  "id" integer primary key autoincrement not null,
  "aggregate_uuid" varchar,
  "aggregate_version" integer,
  "event_version" integer not null default '1',
  "event_class" varchar not null,
  "event_properties" text not null,
  "meta_data" text not null,
  "created_at" datetime not null
);
CREATE INDEX "stored_events_event_class_index" on "stored_events"(
  "event_class"
);
CREATE INDEX "stored_events_aggregate_uuid_index" on "stored_events"(
  "aggregate_uuid"
);
CREATE UNIQUE INDEX "stored_events_aggregate_uuid_aggregate_version_unique" on "stored_events"(
  "aggregate_uuid",
  "aggregate_version"
);
CREATE TABLE IF NOT EXISTS "snapshots"(
  "id" integer primary key autoincrement not null,
  "aggregate_uuid" varchar not null,
  "aggregate_version" integer not null,
  "state" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "snapshots_aggregate_uuid_index" on "snapshots"("aggregate_uuid");
CREATE TABLE IF NOT EXISTS "metrics"(
  "uuid" varchar not null,
  "profile_uuid" varchar not null,
  "type" varchar not null,
  "value" text not null,
  "notes" text,
  "photo_url" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "timestamp" datetime,
  primary key("uuid")
);
CREATE INDEX "metrics_profile_uuid_index" on "metrics"("profile_uuid");
CREATE TABLE IF NOT EXISTS "profiles"(
  "uuid" varchar not null,
  "user_id" integer,
  "instance_uuid" varchar not null,
  "role" varchar not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "last_metric_type" varchar,
  "last_metric_at" datetime,
  "deleted_at" datetime,
  foreign key("user_id") references "users"("id"),
  primary key("uuid")
);
CREATE INDEX "profiles_user_id_instance_uuid_index" on "profiles"(
  "user_id",
  "instance_uuid"
);
CREATE TABLE IF NOT EXISTS "app_instances"(
  "uuid" varchar not null,
  "user_id" integer,
  "profile_uuid" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  primary key("uuid")
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_08_29_174540_create_stored_events_table',1);
INSERT INTO migrations VALUES(5,'2025_08_29_174541_create_snapshots_table',1);
INSERT INTO migrations VALUES(6,'2025_08_29_180701_create_metrics_table',1);
INSERT INTO migrations VALUES(7,'2025_08_29_180702_create_profiles_table',1);
INSERT INTO migrations VALUES(8,'2025_09_01_150834_add_timestamp_to_metrics_table',1);
INSERT INTO migrations VALUES(9,'2025_10_11_012430_create_app_instances_table',1);
INSERT INTO migrations VALUES(10,'2025_10_11_013237_make_user_nullable_at_app_instances',1);
INSERT INTO migrations VALUES(11,'2025_10_11_013912_make_profile_nullable_at_app_instances',1);
INSERT INTO migrations VALUES(12,'2025_10_15_051626_add_last_metric_fields_to_profile',1);
INSERT INTO migrations VALUES(13,'2025_10_22_042216_add_deleted_at_to_profiles_table',1);
