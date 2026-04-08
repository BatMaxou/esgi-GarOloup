import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import TextareaInput from '@/components/ui/molecules/textarea-input';

const TextareaInputs = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Textarea
      </Typography>

      <GlassPanel className="p-8 w-fit">
        <ul className="flex flex-wrap gap-6">
          <li>
            <TextareaInput sizing="sm" name="textarea-sm" rows={3} placeholder="Texte court…" />
          </li>
          <li>
            <TextareaInput name="textarea-md" rows={4} placeholder="Description, message, etc." />
          </li>
          <li>
            <TextareaInput
              sizing="lg"
              name="textarea-lg"
              rows={5}
              placeholder="Zone plus haute pour du contenu long."
            />
          </li>
        </ul>
      </GlassPanel>
    </>
  );
};

export default TextareaInputs;
