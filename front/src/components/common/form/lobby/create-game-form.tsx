'use client';

import { useFormik } from 'formik';
import { useTranslations } from 'next-intl';

import Button from '@/components/ui/molecules/button';
import Divider from '@/components/ui/atoms/divider';
import SwitchInput from '@/components/ui/molecules/switch-input';
import Typography from '@/components/ui/atoms/typography';
import type { CreateGamePayload } from '@/lib/api/resources/GameResource';
import NumberInput from '@/components/ui/molecules/number-input';
import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { toast } from 'react-toastify';
import { paths } from '@/utils/paths';
import { useRouter } from '@/i18n/navigation';

export type CreateGameFormValues = CreateGamePayload;

const CreateGameForm = () => {
  const t = useTranslations('components.common.form.lobby.createGame');
  const { apiClient } = useApiClient();
  const router = useRouter();
  const { values, handleSubmit, handleChange } = useFormik({
    initialValues: {
      maxPlayers: 6,
      maxTimeForDiscussion: 8,
      public: false,
    },
    onSubmit: async (values: CreateGameFormValues) => {
      console.log(values);
      const response = await apiClient.game.create(values);
      if (!(response instanceof ApiClientError)) {
        toast.success(t('createGameSuccess'));
        router.push(paths.game);
        return;
      } else {
        toast.error(t('createGameError'));
      }
    },
  });

  return (
    <form
      className="flex flex-col gap-6"
      method="post"
      onSubmit={(e) => {
        e.preventDefault();
        handleSubmit();
      }}
    >
      <Typography variant="subtitle" bold textColor="light">
        {t('sectionTitle')}
      </Typography>

      <div className="flex flex-col gap-4">
        <div>
          <NumberInput
            name="maxPlayers"
            defaultValue={6}
            min={6}
            max={20}
            unit="joueurs"
            value={values.maxPlayers}
            onChange={(value) => handleChange({ target: { name: 'maxPlayers', value } })}
          />
          <Typography variant="body-xs" textColor="neutral-500" className="mt-1 block">
            {t('maxPlayersHint')}
          </Typography>
        </div>
        <Divider variant="primary" />
        <div className="flex flex-row items-start justify-between gap-4">
          <div className="min-w-0 flex-1">
            <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className="block">
              {t('maxTimeForDiscussionLabel')}
            </Typography>
            <Typography variant="body-sm" textColor="neutral-500" className="mt-1 block">
              {t('maxTimeForDiscussionHint')}
            </Typography>
          </div>
          <NumberInput
            name="maxTimeForDiscussion"
            defaultValue={8}
            value={values.maxTimeForDiscussion}
            onChange={(value) => handleChange({ target: { name: 'maxTimeForDiscussion', value } })}
            min={1}
            max={10}
            unit="min."
          />
        </div>
      </div>

      <Divider variant="primary" />

      <div className="flex flex-row items-start justify-between gap-4">
        <div className="min-w-0 flex-1">
          {values.public ? (
            <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className="block">
              {t('publicLabelPublic')}
            </Typography>
          ) : (
            <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className="block">
              {t('publicLabelPrivate')}
            </Typography>
          )}
          {values.public ? (
            <Typography variant="body-sm" textColor="neutral-500" className="mt-1 block">
              {t('publicHintPublic')}
            </Typography>
          ) : (
            <Typography variant="body-sm" textColor="neutral-500" className="mt-1 block">
              {t('publicHintPrivate')}
            </Typography>
          )}
        </div>
        <SwitchInput
          variant="gradient"
          checked={values.public}
          onChange={() => handleChange({ target: { name: 'public', value: !values.public } })}
          aria-label={t('publicAria')}
        />
      </div>

      <Divider variant="primary" />
      <Button type="submit" variant="accent" label={t('submit')} full />
    </form>
  );
};

export default CreateGameForm;
