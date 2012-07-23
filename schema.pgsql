CREATE TABLE "users" (
  "id" serial,
  "email" character varying(127) NOT NULL,
  "username" character varying(32) NOT NULL DEFAULT '',
  "password" character varying(64) NOT NULL,
  "logins" integer NOT NULL DEFAULT '0',
  "last_login" integer DEFAULT NULL,
  "reset_token" character varying(64) NOT NULL DEFAULT '',
  "status" character varying(20) NOT NULL DEFAULT '',
  "last_failed_login" time NOT NULL DEFAULT '1970-01-01 00:00:00',
  "failed_login_count" integer NOT NULL DEFAULT '0',
  "created" time NOT NULL DEFAULT NOW(),
  "modified" time,
  CONSTRAINT users_pkey PRIMARY KEY ("id"),
  CONSTRAINT users_email_key UNIQUE ("email"),
  CONSTRAINT users_username_key UNIQUE ("username")
);

INSERT INTO "users" ("id", "email", "username", "password", "logins", "last_login") VALUES
(1, 'test@test.com', 'admin', '368ae03c1b3b29b8d242bc43dcbe3f0bd4755ea181adbd22ef', 0, NULL);


CREATE TABLE "roles" (
  "id" serial NOT NULL,
  "name" character varying(32) NOT NULL,
  "description" character varying(255) NOT NULL,
  CONSTRAINT roles_id_pkey PRIMARY KEY ("id"),
  CONSTRAINT roles_name_key UNIQUE ("name")
);

INSERT INTO "roles" ("id", "name", "description") VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.');

-- --------------------------------------------------------

CREATE TABLE "roles_users" (
  "user_id" integer NOT NULL,
  "role_id" integer NOT NULL,
  CONSTRAINT role_id_fkey FOREIGN KEY (role_id)
      REFERENCES roles (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT user_id_fkey FOREIGN KEY (user_id)
      REFERENCES users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);

INSERT INTO "roles_users" ("user_id", "role_id") VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

CREATE TABLE "user_identities" (
  "id" serial,
  "user_id" integer NOT NULL,
  "provider" character varying(255) NOT NULL,
  "identity" character varying(255) NOT NULL,
  CONSTRAINT user_identities_id_pkey PRIMARY KEY ("id")
);

-- --------------------------------------------------------

CREATE TABLE "user_tokens" (
  "id" serial,
  "user_id" integer NOT NULL,
  "user_agent" character varying(40) NOT NULL,
  "token" character varying(40) NOT NULL,
  "created" integer NOT NULL,
  "expires" integer NOT NULL,
  CONSTRAINT user_tokens_id_pkey PRIMARY KEY (id),
  CONSTRAINT user_id_fkey FOREIGN KEY (user_id)
      REFERENCES users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT user_tokens_token_key UNIQUE ("token")
);

-- --------------------------------------------------------

